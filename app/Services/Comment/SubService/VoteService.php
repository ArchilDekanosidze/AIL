<?php
namespace App\Services\Comment\SubService;

use App\Models\User;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Services\Traits\ActorTrait;
use App\Services\Comment\Traits\CommentTrait;

class VoteService
{
    use ActorTrait, CommentTrait;


    public function __construct(Request $request)
    {

        $this->request = $request;

    }

    public function vote()
    {
        $commentId = $this->request->commentId;
        $voteType = $this->request->voteType == 'up' ? 1 : -1;;
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
                $comment->user->incrementScore(-$voteType * User::SCORE_VOTE); // Adjust user reputation
                auth()->user()->incrementScore(-User::SCORE_VOTEING); // Adjust user reputation

                $this->updateUserBadge($comment->user, $comment->question->tag_id, -$voteType * User::SCORE_VOTE);
                $this->updateUserBadge(auth()->user(), $comment->question->tag_id, -User::SCORE_VOTEING);

            }
            else
            {
                $existingVote->update(['value' => $voteType]);
                $comment->increment('score', 2 * $voteType); // Remove old vote and apply new one
                $comment->user->incrementScore(2 * $voteType * User::SCORE_VOTE);
                $this->updateUserBadge($comment->user, $comment->question->tag_id, 2 * $voteType * User::SCORE_VOTE);
            }
            return ['success' => 'رای شما با موفقیت به روز رسانی شد', 'vote' => $comment->score];
        }
        $comment->votes()->create([
            'user_id' => auth()->id(),
            'question_id' => $comment->question_id,
            'value' => $voteType,
        ]);

        auth()->user()->incrementScore(User::SCORE_VOTEING);
        $this->updateUserBadge( auth()->user(), $comment->question->tag_id,  User::SCORE_VOTEING);


        $comment->increment('score', $voteType); // Add vote to total score
        $comment->user->incrementScore($voteType * User::SCORE_VOTE);
        $this->updateUserBadge($comment->user, $comment->question->tag_id, $voteType * User::SCORE_VOTE);

        return ['success' => 'رای شما با موفقیت ثبت شد', 'vote' => $comment->score];
    }



}