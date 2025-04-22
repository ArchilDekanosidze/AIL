<?php
namespace App\Services\Comment\SubService;

use App\Models\User;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\Traits\ActorTrait;
use App\Services\Comment\Traits\CommentTrait;

class BestReplyService
{
    use ActorTrait, CommentTrait;


    public function __construct(Request $request)
    {

        $this->request = $request;

    }

    public function setBestReply()
    {
        $commentId = $this->request->commentId;
        $isBest = $this->request->isBest;

        
        $comment = Comment::find($commentId);

        if ($comment->original_user_id !== auth()->id()) {
            return ['error' => 'شما فقط برای پاسخ های مربوط به کامنت خودتان می توانید بهترین پاسخ را ثبت کنید'];
        } 
        if($isBest == "yes")
        {
            $bestReplyid = $comment->id;
        }
        else
        {
            $bestReplyid = null;
        }

        if(is_null($comment->best_reply_id) &&  !is_null($bestReplyid))
        {
            $comment->user->incrementScore(User::SCORE_BESTREPLY);
            auth()->user()->incrementScore(User::SCORE_SETBESTREPLY);

            $this->updateUserBadge( $comment->user, $comment->question->tag_id, User::SCORE_BESTREPLY);
            $this->updateUserBadge( auth()->user(), $comment->question->tag_id, User::SCORE_SETBESTREPLY);

        }
        if(!is_null($comment->best_reply_id) &&  !is_null($bestReplyid))
        {
            $oldBestReply = Comment::find($comment->best_reply_id);
            $oldBestReply->user->incrementScore(-User::SCORE_BESTREPLY);
            $comment->user->incrementScore(User::SCORE_BESTREPLY);

            $this->updateUserBadge( $oldBestReply->user, $comment->question->tag_id, -User::SCORE_BESTREPLY);
            $this->updateUserBadge( $comment->user, $comment->question->tag_id, User::SCORE_BESTREPLY);



        }
        if(!is_null($comment->best_reply_id) &&  is_null($bestReplyid))
        {
            $comment->user->incrementScore(-User::SCORE_BESTREPLY);
            auth()->user()->incrementScore(-User::SCORE_SETBESTREPLY);

            $this->updateUserBadge( $comment->user, $comment->question->tag_id, -User::SCORE_BESTREPLY);
            $this->updateUserBadge( auth()->user(), $comment->question->tag_id, -User::SCORE_SETBESTREPLY);

        }
        
        
        DB::statement("
        UPDATE comments 
        SET best_reply_id = ? 
        WHERE original_id = ?
        ", [$bestReplyid , $comment->original_id]);

        return ['success' => 'ثبت بهترین نظر شما با موفقیت انجام شد'];

    }




}