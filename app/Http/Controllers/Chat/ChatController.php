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

        $conversations = $user->conversations()
            ->with([
                'lastMessage',
                'participants.user' // we will use this to get names and roles
            ])
            ->get()
            ->map(function ($conversation) use ($user) {
                // Generate display title
                $otherParticipants = $conversation->participants->filter(fn($p) => $p->user_id !== $user->id);

                if (!empty($conversation->title) && $conversation->type !== 'private') {
                    $conversation->display_title = $conversation->title;
                } elseif ($conversation->type === 'private' && $otherParticipants->count()) {
                    $conversation->display_title = $otherParticipants->first()->user->name ?? 'Unknown';
                } elseif (in_array($conversation->type, ['group', 'channel'])) {
                    $names = $otherParticipants->take(3)->pluck('user.name')->toArray();
                    $conversation->display_title = implode(', ', $names) . ($otherParticipants->count() > 3 ? '...' : '');
                } else {
                    $conversation->display_title = 'My Chat';
                }

                // Add unread message count
                $participant = $conversation->participants->firstWhere('user_id', $user->id);
                $lastReadId = $participant?->last_read_message_id ?? 0;

                $conversation->unread_messages_count = $conversation->messages()
                    ->where('id', '>', $lastReadId)
                    ->count();

                return $conversation;
            })
            ->sortByDesc('updated_at') // newest at top like Telegram
            ->values(); // reindex collection

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
