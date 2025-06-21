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
                    if ($conversation->type === 'simple') {
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

    public function searchUsers(Request $request)  
    {
        $term = $request->get('q');
        $currentUserId = Auth::id(); // Get the ID of the currently authenticated user
        return User::where('name', 'like', '%' . $term . '%')
                ->where('id', '!=', $currentUserId) // Exclude the current user
                ->take(10)
                ->get(['id', 'name', 'avatar']); // assuming you have avatar
    }

    public function startConversation(Request $request)  //done
    {
        $authUser = auth()->user();
        $targetUserId = $request->input('user_id');

        // Check if a conversation already exists between these two users
        $conversation = Conversation::whereHas('participants', function ($q) use ($authUser) {
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
                'type' => 'simple'
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
}
