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

class QuestionController extends Controller
{
    public function fetchComments(Request $request)
    {
        $perPage = 5; // Number of comments per request
        $page = $request->page;
        $question_id = $request->question_id;
        $lastCommentId = $request->last_comment_id; // Get last loaded comment ID


        $query = Comment::where('question_id', $question_id)
        ->with('user' , 'parent.user')
        ->orderBy('id', 'desc');


        if ($lastCommentId) {
            $query->where('id', '<', $lastCommentId); // Get comments after the last one
        }
        $comments = $query->take($perPage)
        ->get()
        ->map(function($comment){            
            return $this->mapComment($comment);
        });
        
        return response()->json($comments);
    }

    public function newComments(Request $request)
    {
        $parent_comment_id = $request->parent_comment_id;
        $comment_body = $request->comment_body;
        $question_id = $request->question_id;
        $comment = new Comment();
        $comment->question_id = $question_id;
        $comment->user_id = auth()->user()->id;
        $comment->parent_id = $parent_comment_id;
        $comment->body = $comment_body;
        $comment->save();
        $successMessages = 'کامنت شما با موفقیت  ثبت شد';
        $comment->user->incrementScore(50);
        return  ['successMessages' => $successMessages, 'comment' => $this->mapComment($comment)];
    }

    public function mapComment($comment){            
        return [
            'id' => $comment->id,
            'score' => $comment->score,
            'parent' => [
              'id' =>  $comment->parent ? $comment->parent->id : null,
              'user_id' =>  $comment->parent ? $comment->parent->user->id : null,
              'profile_url' => $comment->parent ? route('profile.student.index', $comment->parent->user->id) : null,
              'user_name' => $comment->parent ? $comment->parent->user->name : null,
              'body' => $comment->parent ? Str::limit($comment->parent->body, 50) : null
            ],
            'body' => $comment->body,
            'user' => [
                'id' => $comment->user->id,
                'name' => $comment->user->name,
                'profile_url' => route('profile.student.index', $comment->user->id)
            ]
            ];
    }
}
