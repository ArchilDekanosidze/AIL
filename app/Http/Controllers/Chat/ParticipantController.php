<?php
namespace App\Http\Controllers\Chat;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Chat\Conversation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ParticipantController extends Controller
{
   public function join(Request $request, Conversation $conversation)
    {
        $user = Auth::user();

        if (!$user) return redirect()->route('auth.login');

        // Prevent joining private conversations
        if ($conversation->type === 'private') {
            return redirect()->back()->with('error', 'Cannot join a private conversation.');
        }

        // Avoid duplicate joins
        if (!$conversation->participants->contains('user_id', $user->id)) {
            $conversation->participants()->create([
                'user_id' => $user->id,
                'role' => 'member', // default role
            ]);
        }

        return redirect()->back()->with('success', 'You joined the conversation!');
    }

}
