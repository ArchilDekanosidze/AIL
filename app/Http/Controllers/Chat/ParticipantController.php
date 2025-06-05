<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Chat\Conversation;
use App\Models\Chat\ConversationParticipant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParticipantController extends Controller
{
    // Show participants of a conversation
    public function index($conversationId)
    {
        $conversation = Conversation::with('participants.user')->findOrFail($conversationId);

        $this->authorize('view', $conversation);

        return view('chat.participants.index', [
            'conversation' => $conversation,
            'participants' => $conversation->participants
        ]);
    }

    // Add a participant to a conversation
    public function add(Request $request, $conversationId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $conversation = Conversation::findOrFail($conversationId);
        $this->authorize('update', $conversation);

        $userId = $request->input('user_id');

        // Prevent duplicates
        $exists = ConversationParticipant::where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->exists();

        if (!$exists) {
            ConversationParticipant::create([
                'conversation_id' => $conversationId,
                'user_id' => $userId,
                'joined_at' => now(),
            ]);
        }

        return redirect()->route('chat.participants.index', $conversationId)
            ->with('success', 'کاربر با موفقیت اضافه شد.');
    }

    // Remove a participant from a conversation
    public function remove(Request $request, $conversationId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $conversation = Conversation::findOrFail($conversationId);
        $this->authorize('update', $conversation);

        ConversationParticipant::where('conversation_id', $conversationId)
            ->where('user_id', $request->user_id)
            ->delete();

        return redirect()->route('chat.participants.index', $conversationId)
            ->with('success', 'کاربر با موفقیت حذف شد.');
    }
}
