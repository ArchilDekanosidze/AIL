<?php
namespace App\Http\Controllers\Question;


use App\Models\Tag;
use App\Models\User;
use App\Models\Comment;
use App\Models\Question;
use Faker\Factory as faker;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CategoryQuestion;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class CommentController extends Controller
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
        if($parent_comment_id)
        {
            $originalComment = Comment::find($parent_comment_id);
            $comment->original_id = $originalComment->original_id;
            $comment->original_user_id = $originalComment->original_user_id;
            $comment->save();

            $originalUser = User::find($comment->original_user_id);
            $originalUser->incrementScore(User::SCORE_REPLY);
            $this->updateUserBadge($originalUser->id, $comment->question->tag_id, User::SCORE_REPLY);
    
        }
        else
        {
            $comment->original_id = $comment->id;
            $comment->original_user_id = $comment->user_id;
            $comment->save();
        }
        $successMessages = 'کامنت شما با موفقیت  ثبت شد';
        $comment->user->incrementScore(User::SCORE_COMMENT);
        $this->updateUserBadge(auth()->user()->id, $comment->question->tag_id, User::SCORE_COMMENT);
        return  ['successMessages' => $successMessages, 'comment' => $this->mapComment($comment)];
    }

    public function mapComment($comment){
        $canMarkAsBest = false;
        if(($comment->original_user_id  === auth()->user()->id) && ($comment->user_id !== auth()->user()->id))
        {
            $canMarkAsBest = true;
        }       
       
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
            'canMarkAsBest' => $canMarkAsBest , 
            'original_id' => $comment->original_id,
            'best_reply_id' => $comment->best_reply_id , 
            'user' => [
                'id' => $comment->user->id,
                'name' => $comment->user->name,
                'profile_url' => route('profile.student.index', $comment->user->id)
            ]
            ];
    }

    public function updateUserBadge($userId, $tagId, $score)
    {     
        $user = User::find($userId);
        $userBadge = $user->badges->where('id', $tagId)->first();
        if(is_null($userBadge))
        {
          $newScore = $score;
        }
        else
        {
          $newScore = $userBadge->pivot->score + $score;
        }
        $tag = Tag::find($tagId);
        $badgeNames = [
          'bronz1',
          'bronz2',
          'bronz3',
          'silver1',
          'silver2',
          'silver3',
          'gold1',
          'gold2',
          'gold3',
          'platinum1',
          'platinum2',
          'platinum3',
          'dimond1',
          'dimond2',
          'dimond3',
          'legendary1',
          'legendary2',
          'legendary3'
        ];
        $newBadge = null;
        foreach ($badgeNames as $badgeName) {
          if($newScore > $tag->{$badgeName})
          {
            $newBadge = $badgeName;
          }
        }
        $user->badges()->syncWithoutDetaching([$tagId => ['score' => $newScore, 'badge' => $newBadge]]);
    }
}
