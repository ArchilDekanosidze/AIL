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

class UpdateFreeQuestionCommentService
{
    use ActorTrait, FreeQuestionTrait;


    public function __construct(Request $request)
    {

        $this->request = $request;

    }


    public function updateComment()
    {
        $commentId = $this->request->route('comment');
        $comment = FreeQuestionComment::findOrFail($commentId);

        // Only the owner can update the comment
        if ($comment->user_id !== auth()->id()) {
            abort(403, 'شما اجازه ویرایش این نظر را ندارید');
        }

        $comment->body = $this->request->comment_body;
        $comment->save();

        $successMessages = 'کامنت شما با موفقیت بروزرسانی شد';

        return ['successMessages' => $successMessages, 'comment' => $this->mapComment($comment)];
    }

   

}