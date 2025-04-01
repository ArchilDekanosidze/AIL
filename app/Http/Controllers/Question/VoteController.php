<?php
namespace App\Http\Controllers\Question;


use App\Models\User;
use App\Models\Comment;
use App\Models\Question;
use Faker\Factory as faker;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CategoryQuestion;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class VoteController extends Controller
{
    public function vote(Request $request)
    {
        $commentId = $request->commentId;
        $voteType = $request->voteType == 'up' ? 1 : -1;;
        $comment = Comment::find($commentId);
        if ($comment->user_id == auth()->id()) {
            return response()->json(['errorSelfvoting' => 'شما نمی توانید به خودتون رای بدهید']);
        }
        $existingVote = $comment->votes()->where('user_id', auth()->id())->first();
        if($existingVote)
        {
            if ($existingVote->value == $voteType) {
                $existingVote->delete();
                $comment->decrement('score', $voteType);
                $comment->user->incrementScore(-$voteType * 10); // Adjust user reputation
            }
            else
            {
                $existingVote->update(['value' => $voteType]);
                $comment->increment('score', 2 * $voteType); // Remove old vote and apply new one
                $comment->user->incrementScore(2 * $voteType * 10);
            }
            return ['success' => 'رای شما با موفقیت به روز رسانی شد', 'vote' => $comment->score];

        }
        $comment->votes()->create([
            'user_id' => auth()->id(),
            'question_id' => $comment->question_id,
            'value' => $voteType,
        ]);

        $comment->increment('score', $voteType); // Add vote to total score
        $comment->user->incrementScore($voteType * 10);
        return ['success' => 'رای شما با موفقیت ثبت شد', 'vote' => $comment->score];

    }
}
