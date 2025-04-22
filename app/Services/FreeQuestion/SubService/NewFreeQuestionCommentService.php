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

class NewFreeQuestionCommentService
{
    use ActorTrait, FreeQuestionTrait;


    public function __construct(Request $request)
    {

        $this->request = $request;

    }


    public function newComment()
    {
        $parent_comment_id = $this->request->parent_comment_id; 
        $comment_body = $this->request->comment_body;
        $free_question_id = $this->request->free_question_id;
        $comment = new FreeQuestionComment();
        $comment->user_id = auth()->user()->id;
        $comment->free_question_id = $free_question_id;
        $comment->parent_id = $parent_comment_id;
        $comment->body = $comment_body;
        $comment->save();      
        
        if($parent_comment_id)
        {
            $parentUser = $comment->parent->user;
            if($parentUser->id != auth()->user()->id)
            {
                $parentUser->incrementScore(User::SCORE_FREEQUESTION_COMMENT_REPLY);
                $this->updateUserBadge($parentUser, $comment->freeQuestion->freeTags->pluck('id'), User::SCORE_FREEQUESTION_COMMENT_REPLY);    
            }
        }
        

        $comment->user->incrementScore(User::SCORE_FREEQUESTION_COMMENT);
        $this->updateUserBadge(auth()->user(), $comment->freeQuestion->freeTags->pluck('id'), User::SCORE_FREEQUESTION_COMMENT);


        $successMessages = 'کامنت شما با موفقیت  ثبت شد';
        return  ['successMessages' => $successMessages, 'comment' => $this->mapComment($comment)];
    }
   

}