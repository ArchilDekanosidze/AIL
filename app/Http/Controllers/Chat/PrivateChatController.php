<?php

namespace App\Http\Controllers\Chat;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Chat\Conversation;
use App\Http\Controllers\Controller;

class PrivateChatController extends Controller
{
    public function start(Request $request)
    {
        $authUser = auth()->user();
        $targetUserId = (int) $request->input('target_user_id');

        // 🔁 Self-chat: redirect to saved messages
        if ($authUser->id === $targetUserId) {
            return redirect()->route('chat.saved-messages');
        }

        // 🔍 Check for existing private conversation
        $conversation = Conversation::where('type', 'private')
            ->whereHas('participants', fn($q) => $q->where('user_id', $authUser->id))
            ->whereHas('participants', fn($q) => $q->where('user_id', $targetUserId))
            ->has('participants', '=', 2)
            ->first();

        // ➕ Create if doesn't exist
        if (!$conversation) {
            $conversation = Conversation::create([
                'type' => 'private',
                'title' => null,
            ]);

            // dd($authUser->id, $targetUserId);
            
            $conversation->participants()->createMany([
                ['user_id' => $authUser->id],
                ['user_id' => $targetUserId],
            ]);
        }

        return redirect()->route('chat.messages.index', $conversation->id);
    }


    public function savedMessages()
    {
        $authUser = auth()->user();

        // Check if a self-chat already exists
        $conversation = Conversation::where('type', 'private')
            ->whereHas('participants', function ($q) use ($authUser) {
                $q->where('user_id', $authUser->id);
            })
            ->has('participants', '=', 1) // only one participant
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'title' => 'ذخیره‌شده‌ها',
                'type' => 'private',
                'owner_id' => $authUser->id
            ]);

            $conversation->participants()->create([
                'user_id' => $authUser->id,
            ]);
        }

        return redirect()->route('chat.messages.index', $conversation->id);
    }

}
