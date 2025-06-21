<?php

namespace App\Http\Controllers\Chat;

use App\Models\Chat\Message;
use Illuminate\Http\Request;
use Hekmatinasser\Verta\Verta; // Keep this for your date formatting
use App\Events\Chat\MessageSent;
use App\Events\Chat\MessageDeleted; // Add this import if you implement MessageDeleted event
use App\Models\Chat\Conversation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Chat\MessageAttachment;
use Illuminate\Support\Facades\Storage;


class MessageController extends Controller
{
    // Show all messages in a conversation
    public function index($conversationId) // Keep your existing $conversationId parameter
    {
        // Use findOrFail for consistency, and eager load participants
        $conversation = Conversation::with('participants.user')->findOrFail($conversationId);
        // $this->authorize('view', $conversation); // Keep your authorization if active

        // Ensure the authenticated user is a participant in this conversation
        if (!$conversation->participants->contains('user_id', Auth::id())) {
            abort(403, 'You are not a participant in this conversation.');
        }

        $user = Auth::user();

        // --- Start: Logic for display_title (Copied from previous suggestion) ---
        if (!empty($conversation->title) && $conversation->type !== 'private') {
            $conversation->display_title = $conversation->title;
        } else {
            // Filter out the current user to find other participants
            $otherParticipants = $conversation->participants->filter(function ($participant) use ($user) {
                return $participant->user_id !== $user->id;
            });

            if ($otherParticipants->count() > 0) {
                if ($conversation->type === 'private') {
                    // For 1-to-1 private chats, show the other participant's name
                    $conversation->display_title = $otherParticipants->first()->user->name ?? 'Unknown User';
                } else if ($conversation->type === 'group' || $conversation->type === 'channel') {
                    // For groups/channels without a title, list a few participant names
                    $names = $otherParticipants->take(3)->pluck('user.name')->toArray();
                    if (!empty($names)) {
                        $conversation->display_title = implode(', ', $names) . ($otherParticipants->count() > 3 ? '...' : '');
                    } else {
                        // Fallback if no other participants (e.g., self-chat in a group/channel context)
                        $conversation->display_title = ucfirst($conversation->type) . ' Chat';
                    }
                } else {
                    // General fallback for unhandled types or edge cases
                    $conversation->display_title = 'Chat Conversation';
                }
            } else {
                // This case handles a conversation where the current user is the only participant
                // (e.g., a self-chat, or a group/channel not yet joined by others)
                $conversation->display_title = 'My Chat';
            }
        }
        // --- End: Logic for display_title ---

        return view('chat.messages.index', compact('conversation'));
    }

    public function getMessages(Request $request, Conversation $conversation)
    {
        // Ensure the authenticated user is a participant
        if (!$conversation->participants->contains('user_id', Auth::id())) {
            return response()->json([], 403);
        }

        $beforeId = $request->query('before');

        // --- Start: Updated query for reactions ---
        $query = $conversation->messages()
            ->with(['sender', 'attachments', 'reactions.user']) // Added 'reactions.user' eager load
            ->orderByDesc('id')
            ->limit(20); // Increased limit to 20 as previously suggested

        if ($beforeId) {
            $query->where('id', '<', $beforeId);
        }

        $messages = $query->get()->reverse()->values();

        // --- Start: Transformation and Reaction Grouping Logic ---
        $transformed = $messages->map(function ($message) {
            $groupedReactions = $message->reactions
                ->groupBy('emoji')
                ->map(function ($group) {
                    return [
                        'emoji' => $group->first()->emoji,
                        'count' => $group->count(),
                        // Map users for each reaction bubble
                        'users' => $group->map(function ($reaction) {
                            return [
                                'id' => $reaction->user->id,
                                'name' => $reaction->user->name,
                            ];
                        })->values()->toArray(),
                    ];
                })->values()->toArray();

            // Determine if current user has reacted and with which emoji
            $currentUserReaction = $message->reactions->firstWhere('user_id', Auth::id());
            $currentUserReactionEmoji = $currentUserReaction ? $currentUserReaction->emoji : null;

            return [
                'id' => $message->id,
                'sender' => [
                    'id' => $message->sender->id ?? null,
                    'name' => $message->sender->name ?? 'Unknown',
                ],
                'content' => $message->content,
                'created_at' => (new Verta($message->created_at))->formatDifference(), // Keep Verta formatting
                'attachments' => $message->attachments->map(function ($att) {
                    return [
                        'id' => $att->id,
                        'file_path' => $att->file_path, // Consider if you want to expose full path or just filename
                        'download_url' => route('chat.attachments.download', $att->id),
                    ];
                }),
                'reactions' => $groupedReactions,          // Add grouped reactions
                'current_user_reaction' => $currentUserReactionEmoji, // Add current user's reaction emoji
            ];
        });
        // --- End: Transformation and Reaction Grouping Logic ---

        return response()->json($transformed);
    }

    // Store a new message
    public function store(Request $request, Conversation $conversation)
    {
        // Ensure user is participant
        if (!$conversation->participants->contains('user_id', Auth::id())) {
            return response()->json(['message' => 'You cannot send messages to this conversation.'], 403);
        }

        $request->validate([
            'content' => 'required_without_all:attachments', // Ensure message content OR attachments are present
            'attachments.*' => 'file|max:10240', // Max 10MB per file, adjust as needed
        ]);

        $message = $conversation->messages()->create([
            'sender_id' => auth()->id(),
            'content' => $request->input('content'),
        ]);

        // Save attachments...
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                // Ensure 'public' disk is specified if you want public access for downloads
                $path = $file->store('chat_attachments', 'public');
                $message->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(), // Store original filename
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        // Eager load sender and attachments for broadcasting and response
        $message->load('sender', 'attachments');

        // For MessageSent event payload, ensure it has `reactions` and `current_user_reaction`
        // New messages have no reactions initially.
        $message->reactions = []; // Initialize empty array for reactions
        $message->current_user_reaction = null; // No user reaction on new message

        // Pass the conversation object to the event if needed by broadcastOn or broadcastWith
        // Make sure MessageSent broadcastWith() extracts the necessary data from $message.
        // If your MessageSent event's broadcastWith() returns the entire $message model,
        // then the `reactions` and `current_user_reaction` properties set above will be included.
        broadcast(new MessageSent($message))->toOthers();

        // Return the full message object for immediate display on sender's side
        // This response should match what `getMessages` returns for a single message for consistency.
        return response()->json([
            'id' => $message->id,
            'sender' => [
                'id' => $message->sender->id ?? null,
                'name' => $message->sender->name ?? 'Unknown',
            ],
            'content' => $message->content,
            'created_at' => (new Verta($message->created_at))->formatDifference(), // Keep Verta formatting
            'attachments' => $message->attachments->map(function ($att) {
                return [
                    'id' => $att->id,
                    'file_path' => $att->file_path, // Or just filename if preferred
                    'download_url' => route('chat.attachments.download', $att->id),
                ];
            }),
            'reactions' => [], // New messages have no reactions
            'current_user_reaction' => null, // New messages have no current user reaction
        ]);
    }

    // --- Start: destroy method (copied from previous suggestion) ---
    /**
     * Remove the specified message from storage.
     *
     * @param  \App\Models\Chat\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(Message $message)
    {
        // Ensure only the sender can delete their own message
        // Or if you want admins/owner to delete any message, add that logic
        if ($message->sender_id !== Auth::id()) {
            abort(403, 'You are not authorized to delete this message.');
        }

        // Delete associated attachments from storage
        foreach ($message->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $conversationId = $message->conversation_id; // Get ID before deletion for broadcast

        $message->delete();

        // Broadcast a MessageDeleted event for real-time update
        // Make sure you have App\Events\Chat\MessageDeleted imported and defined.
        broadcast(new MessageDeleted($message->id, $conversationId))->toOthers();

        return response()->json(['message' => 'Message deleted successfully']);
    }
    // --- End: destroy method ---
}