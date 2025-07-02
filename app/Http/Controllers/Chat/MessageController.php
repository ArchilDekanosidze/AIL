<?php

namespace App\Http\Controllers\Chat;

use Illuminate\Support\Str;
use App\Models\Chat\Message;
use Illuminate\Http\Request;
use Hekmatinasser\Verta\Verta;
use App\Events\Chat\MessageSent;
use App\Models\Chat\Conversation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Chat\MessageAttachment;
use Illuminate\Support\Facades\Storage;
use App\Events\Chat\MessageDeleted; // <-- Keep this import
use App\Events\Chat\MessageEdited; // <-- NEW: Import MessageEdited event
use Illuminate\Database\Eloquent\SoftDeletes; // You need this trait for soft deletes in models

class MessageController extends Controller
{
    // Show all messages in a conversation
    public function index($conversationId)
    {
        $conversation = Conversation::with('participants.user')->findOrFail($conversationId);
        $user = Auth::user();

        $isParticipant = $user && $conversation->participants->contains('user_id', $user->id);

        $participant = $conversation->participants->where('user_id', $user->id)->first();
        $currentUserRole = $participant?->role ?? 'guest';

        // If conversation is private, user must be participant
        if ($conversation->type === 'private' && !$isParticipant) {
            abort(403, 'This is a private conversation.');
        }


        if ($conversation->type != 'private' && $conversation->is_private && !$isParticipant) {
            if (!$participant) {
                return view('chat.messages.access-denied');
            }
        }


        if (!empty($conversation->title) && $conversation->type !== 'private') {
            $conversation->display_title = $conversation->title;
        } else {
            $otherParticipants = $conversation->participants->filter(function ($participant) use ($user) {
                return $participant->user_id !== $user->id;
            });

            if ($otherParticipants->count() > 0) {
                if ($conversation->type === 'private') {
                    $conversation->display_title = $otherParticipants->first()->user->name ?? 'Unknown User';
                } else if ($conversation->type === 'group' || $conversation->type === 'channel') {
                    $names = $otherParticipants->take(3)->pluck('user.name')->toArray();
                    if (!empty($names)) {
                        $conversation->display_title = implode(', ', $names) . ($otherParticipants->count() > 3 ? '...' : '');
                    } else {
                        $conversation->display_title = ucfirst($conversation->type) . ' Chat';
                    }
                } else {
                    $conversation->display_title = 'گفت و گو';
                }
            } else {
                $conversation->display_title = 'چت من';
            }
        }
        $participant = $conversation->participants()->where('user_id', auth()->id())->first();
        if($participant)
        {
            if ($participant->is_banned) {
                return view('chat.messages.banned', compact('conversation'));
            }
        }
        return view('chat.messages.index', compact('conversation', 'currentUserRole'));
    }

    public function getMessages(Request $request, Conversation $conversation)
    {
        $user = Auth::user();
        $isParticipant = $user && $conversation->participants->contains('user_id', $user->id);

        // Guests can read public/group/channel messages
        if ($conversation->type === 'private' && !$isParticipant) {
            return response()->json([], 403);
        }


        $beforeId = $request->query('before');
        $currentUserId = Auth::id(); // Get current user ID for filtering 'deleted_for_user_ids'

        $query = $conversation->messages()
            ->with(['sender.participants' => function ($q) use ($conversation) {
                $q->where('conversation_id', $conversation->id);
            }, 'attachments', 'reactions.user'])
            ->orderByDesc('id')
            ->limit(20);

        if ($beforeId) {
            $query->where('id', '<', $beforeId);
        }

        $messages = $query->get()->values();

        $transformed = $messages->filter(function($message) use ($currentUserId) {
            // Filter out messages soft-deleted (deleted_at is not null) AND
            // messages where current user ID is in deleted_for_user_ids
            return is_null($message->deleted_at) && !in_array($currentUserId, $message->deleted_for_user_ids ?? []);
        })->map(function ($message) {
            $groupedReactions = $message->reactions
                ->groupBy('emoji')
                ->map(function ($group) {
                    return [
                        'emoji' => $group->first()->emoji,
                        'count' => $group->count(),
                        'users' => $group->map(function ($reaction) {
                            return [
                                'id' => $reaction->user->id,
                                'name' => $reaction->user->name,
                            ];
                        })->values()->toArray(),
                    ];
                })->values()->toArray();

            $currentUserReaction = $message->reactions->firstWhere('user_id', Auth::id());
            $currentUserReactionEmoji = $currentUserReaction ? $currentUserReaction->emoji : null;

            return [
                'id' => $message->id,
                'sender' => [
                    'id' => $message->sender->id ?? null,
                    'name' => $message->sender->name ?? 'Unknown',
                    'role' => optional($message->sender->participants->first())->role ?? 'member',
                    'is_banned' => (bool) optional($message->sender->participants->first())->is_banned,
                    'is_muted' => (bool) optional($message->sender->participants->first())->is_muted
                ],
                'content' => $message->content,
                'created_at' => (new Verta($message->created_at))->formatDifference(),
                'edited_at' => $message->edited_at ? (new Verta($message->edited_at))->formatDifference() : null, // <-- Include edited_at
                'attachments' => $message->attachments->map(function ($att) {
                    return [
                        'id' => $att->id,
                        'file_path' => route('chat.attachments.view', $att->id), // <--- Use the new 'view' route for display
                        'file_name' => $att->file_name, // Include file_name
                        'mime_type' => $att->mime_type, // Include mime_type
                        'file_size' => $att->file_size, // Include file_size
                        'download_url' => route('chat.attachments.download', $att->id),
                    ];
                }),
                'reactions' => $groupedReactions,
                'current_user_reaction' => $currentUserReactionEmoji,
            ];
        })->values(); // Re-index the collection after filtering

        return response()->json($transformed);
    }

    public function store(Request $request, Conversation $conversation)
    {

        if (Auth::user()->is_muted || Auth::user()->is_banned) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $user = Auth::user();

        if (!$user || !$conversation->participants->contains('user_id', $user->id)) {
            return response()->json(['message' => 'You cannot send messages to this conversation.'], 403);
        }


        $request->validate([
            'content' => 'required_without_all:attachments',
            'attachments.*' => 'file|max:10240',
        ]);

        $message = $conversation->messages()->create([
            'sender_id' => auth()->id(),
            'content' => $request->input('content'),
            'edited_at' => null, // New messages are not edited
            'deleted_for_user_ids' => [], // New messages are not deleted for anyone
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                    // Get current date and time
                $now = now(); // Uses Carbon, which is part of Laravel
                $year = $now->format('Y');      // e.g., 2025
                $month = $now->format('m');     // e.g., 06
                $day = $now->format('d');       // e.g., 22
                $hour = $now->format('H');      // e.g., 15 (24-hour format)
                $minute = $now->format('i');    // e.g., 21
                // Define the base directory structure within 'chat_attachments'
                $baseDirectory = "chat_attachments/{$year}/{$month}/{$day}/{$hour}/{$minute}";
                // Generate a unique filename to prevent conflicts, preserving the original extension
                // Using Str::uuid() is highly recommended for robust uniqueness.
                $originalExtension = $file->getClientOriginalExtension();
                $uniqueFileName = Str::uuid() . '.' . $originalExtension; // e.g., 'a1b2c3d4-e5f6-...f7g8.jpg'

                // Construct the full path that will be stored in the database
                $fullFilePathInStorage = "{$baseDirectory}/{$uniqueFileName}";

                // Store the file using putFileAs, specifying the directory and the new filename
                // The 'private' disk means it will be saved in storage/app/
                Storage::disk('private')->putFileAs($baseDirectory, $file, $uniqueFileName);

                $message->attachments()->create([
                    'file_path' => $fullFilePathInStorage,
                    'file_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        $message->load('sender', 'attachments');

        // Prepare message data for broadcasting, ensuring it matches frontend renderMessage expectations
        $senderParticipant = $conversation->participants->firstWhere('user_id', $message->sender_id);
        $senderRole = $senderParticipant?->role ?? 'member';
        $messageData = [
            'id' => $message->id,
            'conversation_id' => $conversation->id, // <--- ENSURE THIS LINE IS PRESENT AND CORRECT
            'sender' => [
                'id' => $message->sender->id ?? null,
                'name' => $message->sender->name ?? 'Unknown',
                'role' => $senderRole, 
            ],
            'content' => $message->content,
            'created_at' => (new Verta($message->created_at))->formatDifference(),
            'edited_at' => null, // New messages are not edited
            'attachments' => $message->attachments->map(function ($att) {
               return [
                    'id' => $att->id,
                    'file_path' => route('chat.attachments.view', $att->id), // <--- Use the new 'view' route for display
                    'file_name' => $att->file_name, // Include file_name
                    'mime_type' => $att->mime_type, // Include mime_type
                    'file_size' => $att->file_size, // Include file_size
                    'download_url' => route('chat.attachments.download', $att->id),
                ];
            })->toArray(),
            'reactions' => [], // New messages have no reactions
            'current_user_reaction' => null, // No current user reaction
            'deleted_at' => null, // Ensure these are null for new message
            'deleted_for_user_ids' => [],
        ];

        broadcast(new MessageSent($messageData))->toOthers(); // Pass the array

        return response()->json($messageData); // Return the same array to sender
    }

    /**
     * Update the specified message in storage.
     * This method handles the "edit message" functionality.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Chat\Message  $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Message $message)
    {

        if (Auth::user()->is_muted || Auth::user()->is_banned) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Authorization: Only the sender can edit their own message
        if ($message->sender_id !== Auth::id()) {
            return response()->json(['message' => 'You are not authorized to edit this message.'], 403);
        }

        // Validate the request
        $request->validate([
            'content' => 'required|string|max:5000', // Adjust max length as needed
        ]);

        // Update message content and set edited_at timestamp
        $message->content = $request->input('content');
        $message->edited_at = now(); // Set the current timestamp when message is edited
        $message->save();

        // Reload necessary relationships for the broadcast and response
        $message->load('sender', 'attachments', 'reactions.user');

        // Transform the message for consistent frontend consumption
        $transformedMessage = [
            'id' => $message->id,
            'conversation_id' => $message->conversation_id, // <--- ADDED FOR BROADCASTING
            'sender' => [
                'id' => $message->sender->id ?? null,
                'name' => $message->sender->name ?? 'Unknown',
            ],
            'content' => $message->content,
            'created_at' => (new Verta($message->created_at))->formatDifference(),
            'edited_at' => (new Verta($message->edited_at))->formatDifference(), // Formatted edited_at
            'attachments' => $message->attachments->map(function ($att) {
              return [
                    'id' => $att->id,
                    'file_path' => route('chat.attachments.view', $att->id), // <--- Use the new 'view' route for display
                    'file_name' => $att->file_name, // Include file_name
                    'mime_type' => $att->mime_type, // Include mime_type
                    'file_size' => $att->file_size, // Include file_size
                    'download_url' => route('chat.attachments.download', $att->id),
                ];
            })->toArray(),
            'reactions' => $message->reactions->groupBy('emoji')->map(function ($group) {
                return [
                    'emoji' => $group->first()->emoji,
                    'count' => $group->count(),
                    'users' => $group->map(fn($reaction) => ['id' => $reaction->user->id, 'name' => $reaction->user->name])->values()->toArray(),
                ];
            })->values()->toArray(),
            'current_user_reaction' => $message->reactions->firstWhere('user_id', Auth::id())?->emoji,
        ];

        // Broadcast an event that the message was updated
        broadcast(new MessageEdited($transformedMessage))->toOthers();

        return response()->json(['message' => 'Message updated successfully', 'updated_message' => $transformedMessage]);
    }

    /**
     * Remove the specified message from storage based on deletion type.
     * This method handles both "delete for everyone" and "delete for myself".
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Chat\Message  $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, Message $message)
    {
        $currentUser = Auth::user();
        $currentUserId = $currentUser->id;
        $conversation = $message->conversation;

        if ($currentUser->is_muted || $currentUser->is_banned) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // If user is sender, allow deletion
        if ($message->sender_id === $currentUserId) {
            // proceed with deletion
        } else {
            // Otherwise, check roles & permissions
            $participant = $conversation->participants()->where('user_id', $currentUserId)->first();
            $targetParticipant = $conversation->participants()->where('user_id', $message->sender_id)->first();

            if (!$participant) {
                return response()->json(['message' => 'You are not authorized to delete this message.'], 403);
            }

            if ($participant->role === 'super_admin') {
                // super_admin can delete any message
            } elseif ($participant->role === 'admin') {
                // admin can delete only messages from members
                if (!$targetParticipant || $targetParticipant->role !== 'member') {
                    return response()->json(['message' => 'You are not authorized to delete this message.'], 403);
                }
            } else {
                return response()->json(['message' => 'You are not authorized to delete this message.'], 403);
            }
        }

        // Proceed with deletion logic

        $deletionType = $request->input('deletion_type'); // 'for_everyone' or 'for_me'
        $conversationId = $conversation->id;

        if ($deletionType === 'for_everyone') {
            foreach ($message->attachments as $attachment) {
                Storage::disk('private')->delete($attachment->file_path);
            }
            $message->delete();

            broadcast(new MessageDeleted($message->id, $conversationId, 'for_everyone', $currentUserId))->toOthers();

        } elseif ($deletionType === 'for_me') {
            $deletedFor = $message->deleted_for_user_ids ?? [];
            if (!in_array($currentUserId, $deletedFor)) {
                $deletedFor[] = $currentUserId;
            }
            $message->deleted_for_user_ids = $deletedFor;
            $message->save();

            broadcast(new MessageDeleted($message->id, $conversationId, 'for_me', $currentUserId));

        } else {
            return response()->json(['message' => 'Invalid deletion type.'], 400);
        }

        return response()->json(['message' => 'Message deletion processed successfully.']);
    }


    public function accessBySlug($slug)
    {
        $conversation = Conversation::where('slug', $slug)->firstOrFail();

        $user = auth()->user();

        // If the user is not yet a participant, auto-attach them for both public and private channels
        if ($user) {
            $alreadyJoined = $conversation->participants()->where('user_id', $user->id)->exists();

            if (!$alreadyJoined) {
                $conversation->participants()->create([
                    'user_id' => $user->id,
                    'role' => 'member',
                    'joined_at' => now(),
                ]);
            }
        }

        return redirect()->route('chat.messages.index', $conversation->id);
    }


}