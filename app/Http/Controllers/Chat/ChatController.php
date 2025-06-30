<?php

namespace App\Http\Controllers\Chat;

use App\Models\User;
use App\Models\Chat\Message;
use Illuminate\Http\Request;
use App\Models\Chat\Reaction;
use App\Models\Chat\Conversation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Chat\MessageAttachment;
use Illuminate\Support\Facades\Storage;
use App\Models\Chat\ConversationParticipant;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Eager load lastMessage and participants.user for performance
        $conversations = $user->conversations()
            ->with(['lastMessage', 'participants.user']) // Keep your eager loads
            ->latest('updated_at') // Add sorting to show recent conversations first
            ->get();

        // Process each conversation to determine its display title
        foreach ($conversations as $conversation) {
            // Default to the conversation's title if it has one and is not a private chat,
            // or if it's a group/channel that has a specific title set.
            if (!empty($conversation->title) && $conversation->type !== 'private') {
                $conversation->display_title = $conversation->title;
            } else {
                // For private chats or groups/channels without an explicit title, generate one.
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
        }

        return view('chat.home.index', compact('conversations'));
    }

    public function create() 
    {
        return view('chat.home.create');
    }

    public function searchEntities(Request $request)
    {
       
        $term = $request->get('q');
        $currentUserId = auth()->id();

        // Step 1: Find users who banned current user
        $blockedConversationIds = \App\Models\Chat\ConversationParticipant::where('user_id', $currentUserId)
            ->where('is_banned', true)
            ->whereHas('conversation', fn($q) => $q->where('type', 'private'))
            ->pluck('conversation_id');

        $usersWhoBannedCurrent = \App\Models\Chat\ConversationParticipant::whereIn('conversation_id', $blockedConversationIds)
            ->where('user_id', '!=', $currentUserId)
            ->pluck('user_id');

        // Step 2: Search users excluding those who banned you
        $users = User::where('name', 'like', "%$term%")
            ->where('id', '!=', $currentUserId)
            ->whereNotIn('id', $usersWhoBannedCurrent)
            ->take(5)
            ->get(['id', 'name', 'avatar'])
            ->map(function ($user) {
                return [
                    'type' => 'user',
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar' => $user->avatar ?? '/images/Site/default-avatar.png',
                ];
            });

        // Step 3: Search public groups/channels where you're not banned
        $conversations = Conversation::where('title', 'like', "%$term%")
            ->where('is_private', false)
            ->whereIn('type', ['group', 'channel'])
            ->whereDoesntHave('participants', function ($q) use ($currentUserId) {
                $q->where('user_id', $currentUserId)
                ->where('is_banned', true);
            })
            ->take(5)
            ->get(['id', 'title', 'type', 'avatar'])
            ->map(function ($conv) {
                return [
                    'type' => 'conversation',
                    'id' => $conv->id,
                    'name' => $conv->title,
                    'conversation_type' => $conv->type,
                    'avatar' => asset('storage/' . $conv->avatar)  ?? '/images/Site/default-group.png',
                ];
            });

        return response()->json(collect($users)->merge(collect($conversations))->values());
    }




    public function startConversation(Request $request)  //done
    {
        $authUser = auth()->user();
        $targetUserId = $request->input('user_id');

        // Check if a conversation already exists between these two users
        $conversation = Conversation::where('type', 'private')
        ->whereHas('participants', function ($q) use ($authUser) {
            $q->where('user_id', $authUser->id);
        })
        ->whereHas('participants', function ($q) use ($targetUserId) {
            $q->where('user_id', $targetUserId);
        })
        ->has('participants', '=', 2) // Make sure it's only a 2-person chat
        ->first();

        if (!$conversation) {
            // Create new conversation
            $conversation = Conversation::create([
                'title' => null, // or auto generate title
                'type' => 'private'
            ]);
            $conversation->participants()->create([
                'user_id' => $authUser->id,
            ]);
            $conversation->participants()->create([
                'user_id' => $targetUserId,
            ]);
        }

        return response()->json([
            'conversation_id' => $conversation->id,
            'redirect_url' => route('chat.messages.index', $conversation->id)
        ]);
    }

    public function toggleBanUser(Request $request)
    {
        $authUser = auth()->user();
        $targetUserId = $request->input('user_id');

        if ($authUser->id == $targetUserId) {
            return back()->with('error', 'شما نمی‌توانید خودتان را مسدود یا رفع مسدودی کنید.');
        }

        // Find or create private conversation
        $conversation = Conversation::where('type', 'private')
            ->whereHas('participants', fn($q) => $q->where('user_id', $authUser->id))
            ->whereHas('participants', fn($q) => $q->where('user_id', $targetUserId))
            ->has('participants', '=', 2)
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create(['type' => 'private']);
            $conversation->participants()->create(['user_id' => $authUser->id]);
            $conversation->participants()->create(['user_id' => $targetUserId]);
        }

        // Toggle ban
        $participant = $conversation->participants()->where('user_id', $targetUserId)->first();
        if ($participant) {
            $participant->is_banned = !$participant->is_banned;
            $participant->save();
            $msg = $participant->is_banned ? 'کاربر با موفقیت مسدود شد.' : 'مسدودیت این کاربر برداشته شد.';
            return back()->with('success', $msg);
        }

        return back()->with('error', 'عملیات با شکست مواجه شد.');
    }


}
