<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Chat\Message;
use App\Models\Chat\Reaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReactionController extends Controller
{
    /**
     * Store or toggle a reaction.
     */
    public function store(Request $request, $messageId)
    {
        $request->validate([
            'emoji' => 'required|string|max:191',
        ]);

        $user = Auth::user();
        $message = Message::with('conversation.participants')->findOrFail($messageId);

        // Ensure user is participant of the conversation
        $isParticipant = $message->conversation
            ->participants()
            ->where('user_id', $user->id)
            ->exists();

        if (!$isParticipant) {
            return response()->json(['error' => 'شما اجازه انجام این عملیات را ندارید.'], 403);
        }

        // Toggle reaction
        $existing = Reaction::where('user_id', $user->id)
            ->where('message_id', $message->id)
            ->where('emoji', $request->emoji)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['status' => 'removed']);
        } else {
            Reaction::create([
                'user_id' => $user->id,
                'message_id' => $message->id,
                'emoji' => $request->emoji,
            ]);
            return response()->json(['status' => 'added']);
        }
    }

    /**
     * Optionally: fetch all reactions for a message.
     */
    public function index($messageId)
    {
        $message = Message::with('reactions.user')->findOrFail($messageId);

        return response()->json([
            'reactions' => $message->reactions->groupBy('emoji')->map(function ($group) {
                return $group->pluck('user.name');
            }),
        ]);
    }
}
