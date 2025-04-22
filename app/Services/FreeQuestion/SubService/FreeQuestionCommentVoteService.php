<?php
namespace App\Services\FreeQuestion\SubService;

use App\Models\User;
use App\Models\Comment;
use App\Models\FreeTag;
use App\Models\FreeQuestion;
use Illuminate\Http\Request;
use App\Models\FreeQuestionComment;
use App\Services\Traits\ActorTrait;
use App\Services\Comment\Traits\CommentTrait;
use App\Services\FreeQuestion\Traits\FreeQuestionTrait;

class FreeQuestionCommentVoteService
{
    use ActorTrait, FreeQuestionTrait;


    public function __construct(Request $request)
    {

        $this->request = $request;

    }


    public function vote()
    {
        $commentId = $this->request->commentId;
        $voteType = $this->request->voteType == 'up' ? 1 : -1; 


        $freeQuestionComment = FreeQuestionComment::find($commentId);
        if ($freeQuestionComment->user_id == auth()->id()) {
            return response()->json(['errorSelfvoting' => 'شما نمی توانید به خودتون رای بدهید']);
        }

        $existingVote = $freeQuestionComment->freeQuestionCommentVotes()->where('user_id', auth()->id())->first();

        if($existingVote)
        {
            if ($existingVote->value == $voteType) {
                $existingVote->delete();
                $freeQuestionComment->decrement('score', $voteType);

                $freeQuestionComment->user->incrementScore(-$voteType * User::SCORE_FREE_COMMENT_VOTE); // Adjust user reputation
                auth()->user()->incrementScore(-User::SCORE_FREE_COMMENT_VOTEING); // Adjust user reputation
                $this->updateUserBadge($freeQuestionComment->user, $freeQuestionComment->freeQuestion->freeTags->pluck('id'), -$voteType * User::SCORE_FREE_COMMENT_VOTE);
                $this->updateUserBadge(auth()->user(), $freeQuestionComment->freeQuestion->freeTags->pluck('id'), -User::SCORE_FREE_COMMENT_VOTEING);

            }
            else
            {
                $existingVote->update(['value' => $voteType]);
                $freeQuestionComment->increment('score', 2 * $voteType); // Remove old vote and apply new one

                $freeQuestionComment->user->incrementScore(2 * $voteType * User::SCORE_FREE_COMMENT_VOTE);
                $this->updateUserBadge($freeQuestionComment->user, $freeQuestionComment->freeQuestion->freeTags->pluck('id'), 2 * $voteType * User::SCORE_FREE_COMMENT_VOTE);



            }
            return ['success' => 'رای شما با موفقیت به روز رسانی شد', 'vote' => $freeQuestionComment->score];
        }

        $freeQuestionComment->freeQuestionCommentVotes()->create([
            'user_id' => auth()->id(),
            'free_question_id' => $freeQuestionComment->id,
            'value' => $voteType,
        ]);

        auth()->user()->incrementScore(User::SCORE_FREE_COMMENT_VOTEING);
        $this->updateUserBadge( auth()->user(), $freeQuestionComment->freeQuestion->freeTags->pluck('id'),  User::SCORE_FREE_COMMENT_VOTEING);


        $freeQuestionComment->increment('score', $voteType); // Add vote to total score

        $freeQuestionComment->user->incrementScore($voteType * User::SCORE_FREE_COMMENT_VOTE);
        $this->updateUserBadge($freeQuestionComment->user, $freeQuestionComment->freeQuestion->freeTags->pluck('id'), $voteType * User::SCORE_FREE_COMMENT_VOTE);


        return ['success' => 'رای شما با موفقیت ثبت شد', 'vote' => $freeQuestionComment->score];
    }
   

}