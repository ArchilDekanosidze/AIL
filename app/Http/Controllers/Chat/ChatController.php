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

        // Eager load lastMessage for performance
        $conversations = $user->conversations()
            ->with(['lastMessage', 'participants.user'])
            ->get();

        return view('chat.home.index', compact('conversations'));
    }

    public function create() 
    {
        return view('chat.home.create');
    }

    public function searchUsers(Request $request)  
    {
        $term = $request->get('q');
        return User::where('name', 'like', '%' . $term . '%')
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
