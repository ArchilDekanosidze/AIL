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

class BestReplyController extends Controller
{
    public function setBestReply(Request $request)
    {
        $commentId = $request->commentId;
        $replyId = $request->replyId;
        
        $comment = Comment::find($commentId);
        $reply = Comment::find($replyId);

        // Ensure only the owner of the comment can set the best reply
        if ($comment->user_id !== auth()->id()) {
            return response()->json(['message' => 'You can only set a best reply for your own comments.'], 403);
        }

        // Ensure the reply belongs to this comment
        if ($reply->parent_id !== $comment->id) {
            return response()->json(['message' => 'This reply does not belong to your comment.'], 400);
        }

        // Set or unset best reply
        $comment->best_reply_id = ($comment->best_reply_id == $replyId) ? null : $replyId;
        $comment->save();

        return response()->json([
            'success' => true,
            'best_reply_id' => $comment->best_reply_id,
        ]);
    }
}
