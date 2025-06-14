<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Chat\Message;
use App\Models\Chat\Reaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\Chat\MessageReactionUpdated; // Make sure to import your event

class ReactionController extends Controller
{
    /**
     * Store or toggle a reaction for a message.
     * A user can have only one reaction per message.
     * Clicking the same emoji again removes their reaction.
     * Clicking a different emoji changes their existing reaction.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $messageId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $messageId)
    {
        $request->validate([
            'emoji' => 'required|string|max:191', // Validate the emoji character
        ]);

        $user = Auth::user();
        $message = Message::with('conversation.participants')->findOrFail($messageId);

        // Ensure the authenticated user is a participant in the conversation
        $isParticipant = $message->conversation
            ->participants()
            ->where('user_id', $user->id)
            ->exists();

        if (!$isParticipant) {
            return response()->json(['error' => 'You are not authorized to perform this operation.'], 403);
        }

        // Check if the user already has any reaction on this specific message
        $existingReaction = Reaction::where('user_id', $user->id)
            ->where('message_id', $message->id)
            ->first();

        $status = ''; // To indicate whether reaction was 'added', 'removed', or 'changed'

        if ($existingReaction) {
            // User already has a reaction on this message
            if ($existingReaction->emoji === $request->emoji) {
                // If the submitted emoji is the same as the existing one, delete the reaction (toggle off)
                $existingReaction->delete();
                $status = 'removed';
            } else {
                // If the submitted emoji is different, update the existing reaction to the new emoji
                $existingReaction->update(['emoji' => $request->emoji]);
                $status = 'changed';
            }
        } else {
            // User does not have a reaction on this message yet, so create a new one
            Reaction::create([
                'user_id' => $user->id,
                'message_id' => $message->id,
                'emoji' => $request->emoji, // Use the 'emoji' column
            ]);
            $status = 'added';
        }

        // Broadcast an event to all connected clients in the conversation to update the UI in real-time
        // The toOthers() method ensures the event is not broadcast back to the client that triggered it,
        // as their UI is updated immediately by the success callback of the AJAX request.
        broadcast(new MessageReactionUpdated(
            $message->id,
            $user->id,
            $request->emoji,
            $status
        ))->toOthers();

        // Return a success response
        return response()->json(['status' => $status]);
    }

    /**
     * Fetch all reactions for a given message, grouped by emoji,
     * including count, user names, and indicating if the current user reacted.
     *
     * @param  int  $messageId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($messageId)
    {
        $message = Message::with('reactions.user')->findOrFail($messageId);
        $user = Auth::user(); // Get the currently authenticated user

        // Group reactions by emoji and transform them into the desired frontend format
        $reactionsGrouped = $message->reactions
            ->groupBy('emoji')
            ->map(function ($group) {
                return [
                    'emoji' => $group->first()->emoji, // Get the emoji character for this group
                    'count' => $group->count(),        // Get the total count for this emoji reaction
                    'users' => $group->pluck('user.name')->toArray(), // Get an array of user names who reacted with this emoji
                ];
            })
            ->values() // Convert the collection of grouped items back to a numerically indexed array
            ->sortByDesc('count') // Sort reactions by count (most popular first) for consistent display
            ->toArray(); // Convert to array for final JSON response

        // Determine if the current authenticated user has reacted to this message, and if so, with which emoji
        $currentUserReaction = $message->reactions
                                        ->where('user_id', $user->id)
                                        ->first();

        return response()->json([
            'message_id' => $messageId,
            'reactions' => $reactionsGrouped,
            'current_user_reaction' => $currentUserReaction ? $currentUserReaction->emoji : null,
        ]);
    }
}