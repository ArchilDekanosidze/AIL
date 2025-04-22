<?php
namespace App\Services\Comment\SubService;

use App\Models\User;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Services\Traits\ActorTrait;
use App\Services\Comment\Traits\CommentTrait;

class NewCommentService
{
    use ActorTrait, CommentTrait;


    public function __construct(Request $request)
    {

        $this->request = $request;

    }


    public function newComment()
    {
        $parent_comment_id = $this->request->parent_comment_id;
        $comment_body = $this->request->comment_body;
        $question_id = $this->request->question_id;
        $comment = new Comment();
        $comment->question_id = $question_id;
        $comment->user_id = auth()->user()->id;
        $comment->parent_id = $parent_comment_id;
        $comment->body = $comment_body;
        $comment->save();
        if($parent_comment_id)
        {
            $originalComment = Comment::find($parent_comment_id);
            $comment->original_id = $originalComment->original_id;
            $comment->original_user_id = $originalComment->original_user_id;
            $comment->save();

            $originalUser = User::find($comment->original_user_id);
            if($originalUser->id != auth()->user()->id)
            {
                $originalUser->incrementScore(User::SCORE_REPLY);
                $this->updateUserBadge($originalUser, $comment->question->tag_id, User::SCORE_REPLY);
            }                
        }
        else
        {
            $comment->original_id = $comment->id;
            $comment->original_user_id = $comment->user_id;
            $comment->save();
        }
        $successMessages = 'کامنت شما با موفقیت  ثبت شد';
        $comment->user->incrementScore(User::SCORE_COMMENT);
        $this->updateUserBadge(auth()->user(), $comment->question->tag_id, User::SCORE_COMMENT);
        return  ['successMessages' => $successMessages, 'comment' => $this->mapComment($comment)];
    }
   

}