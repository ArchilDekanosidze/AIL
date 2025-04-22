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

class FreeQuestionBestReplyService
{
    use ActorTrait, FreeQuestionTrait;


    public function __construct(Request $request)
    {

        $this->request = $request;

    }


    public function setBestReply()
    {
        $commentId = $this->request->commentId;
        $isBest = $this->request->isBest;

        
        $freeQuestionComment = FreeQuestionComment::find($commentId);
        $freeQuestion = $freeQuestionComment->freeQuestion;


        if ($freeQuestion->user_id !== auth()->id()) {
            return ['errorSelfvoting' => 'شما فقط برای پاسخ های مربوط به کامنت خودتان می توانید بهترین پاسخ را ثبت کنید'];
        } 
        if($isBest == "yes")
        {
            $bestReplyid = $freeQuestionComment->id;
        }
        else
        {
            $bestReplyid = null;
        }
        

        if(is_null($freeQuestion->best_reply_id) &&  !is_null($bestReplyid))
        {
          $freeQuestionComment->user->incrementScore(User::SCORE_FREE_BESTREPLY);
          auth()->user()->incrementScore(User::SCORE_FREE_SETBESTREPLY);

          $this->updateUserBadge( $freeQuestionComment->user, $freeQuestionComment->freeQuestion->freeTags->pluck('id'), User::SCORE_FREE_BESTREPLY);
          $this->updateUserBadge( auth()->user(), $freeQuestionComment->freeQuestion->freeTags->pluck('id'), User::SCORE_FREE_SETBESTREPLY);

        }
        if(!is_null($freeQuestion->best_reply_id) &&  !is_null($bestReplyid))
        {
            $oldBestReply = FreeQuestionComment::find($freeQuestion->best_reply_id);
            $oldBestReply->user->incrementScore(-User::SCORE_FREE_BESTREPLY);
            $freeQuestionComment->user->incrementScore(User::SCORE_FREE_BESTREPLY);

            $this->updateUserBadge( $oldBestReply->user, $freeQuestionComment->freeQuestion->freeTags->pluck('id'), -User::SCORE_FREE_BESTREPLY);
            $this->updateUserBadge( $freeQuestionComment->user, $freeQuestionComment->freeQuestion->freeTags->pluck('id'), User::SCORE_FREE_BESTREPLY);

        }
        if(!is_null($freeQuestion->best_reply_id) &&  is_null($bestReplyid))
        {

          $freeQuestionComment->user->incrementScore(-User::SCORE_FREE_BESTREPLY);
          auth()->user()->incrementScore(-User::SCORE_FREE_SETBESTREPLY);

          $this->updateUserBadge( $freeQuestionComment->user, $freeQuestionComment->freeQuestion->freeTags->pluck('id'), -User::SCORE_FREE_BESTREPLY);
          $this->updateUserBadge( auth()->user(), $freeQuestionComment->freeQuestion->freeTags->pluck('id'), -User::SCORE_FREE_SETBESTREPLY);


        }
        
        $freeQuestion->best_reply_id = $bestReplyid;
        $freeQuestion->save();


        return ['success' => 'ثبت بهترین نظر شما با موفقیت انجام شد'];
    }


}