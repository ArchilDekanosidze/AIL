<?php
namespace App\Services\FreeQuestion\SubService;

use App\Models\User;
use App\Models\Comment;
use App\Models\FreeTag;
use App\Models\FreeQuestion;
use Illuminate\Http\Request;
use App\Services\Traits\ActorTrait;
use App\Services\Comment\Traits\CommentTrait;
use App\Services\FreeQuestion\Traits\FreeQuestionTrait;

class FreeQuestionVoteService
{
    use ActorTrait, FreeQuestionTrait;


    public function __construct(Request $request)
    {

        $this->request = $request;

    }


    public function vote()
    {
        $freeQuestionId = $this->request->freeQuestionId;
        $voteType = $this->request->voteType == 'up' ? 1 : -1;

        $freeQuestion = FreeQuestion::find($freeQuestionId);
        if ($freeQuestion->user_id == auth()->id()) {
            return response()->json(['errorSelfvoting' => 'شما نمی توانید به خودتون رای بدهید']);
        }

        $existingVote = $freeQuestion->freeQuestionVotes()->where('user_id', auth()->id())->first();

        if($existingVote)
        {
            if ($existingVote->value == $voteType) {
                $existingVote->delete();
                $freeQuestion->decrement('score', $voteType);

                $freeQuestion->user->incrementScore(-$voteType * User::SCORE_FREE_VOTE); // Adjust user reputation
                auth()->user()->incrementScore(-User::SCORE_VOTEING); // Adjust user reputation

                $this->updateUserBadge($freeQuestion->user, $freeQuestion->freeTags->pluck('id'), -$voteType * User::SCORE_FREE_VOTE);
                $this->updateUserBadge(auth()->user(), $freeQuestion->freeTags->pluck('id'), -User::SCORE_FREE_VOTEING);
            }
            else
            {
                $existingVote->update(['value' => $voteType]);
                $freeQuestion->increment('score', 2 * $voteType); // Remove old vote and apply new one

                $freeQuestion->user->incrementScore(2 * $voteType * User::SCORE_FREE_VOTE);
                $this->updateUserBadge($freeQuestion->user, $freeQuestion->freeTags->pluck('id'), 2 * $voteType * User::SCORE_FREE_VOTE);



            }
            return ['success' => 'رای شما با موفقیت به روز رسانی شد', 'vote' => $freeQuestion->score];
        }

        $freeQuestion->freeQuestionVotes()->create([
            'user_id' => auth()->id(),
            'free_question_id' => $freeQuestion->id,
            'value' => $voteType,
        ]);

        auth()->user()->incrementScore(User::SCORE_FREE_VOTEING);
        $this->updateUserBadge( auth()->user(), $freeQuestion->freeTags->pluck('id'),  User::SCORE_FREE_VOTEING);


        $freeQuestion->increment('score', $voteType); // Add vote to total score


        $freeQuestion->user->incrementScore($voteType * User::SCORE_FREE_VOTE);
        $this->updateUserBadge($freeQuestion->user, $freeQuestion->freeTags->pluck('id'), $voteType * User::SCORE_FREE_VOTE);


        return ['success' => 'رای شما با موفقیت ثبت شد', 'vote' => $freeQuestion->score];
    }
   

}