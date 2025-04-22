<?php
namespace App\Services\FreeQuestion\Traits;

use App\Models\Tag;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Support\Str;
use App\Models\FreeQuestion;
use Illuminate\Http\Request;
use App\Models\FreeQuestionComment;


trait FreeQuestionTrait
{
  public function mapFreeQuestion($freeQuestion){
        
    $tagsModels = $freeQuestion->freeTags;
    foreach ($tagsModels as $tagsModel ) {
        $tempTag['name'] =  $tagsModel->name;
        $tempTag['id'] = $tagsModel->id;
        $tempTag['slug'] =  $tagsModel->slug;
        $tags[] = $tempTag;
    }

   
    return [
        'id' => $freeQuestion->id,
        'score' => $freeQuestion->score,  
        'head' => $freeQuestion->head ,           
        'body' => $freeQuestion->body,
        'voteCount' => count($freeQuestion->freeQuestionVotes),
        'commentCount' => count($freeQuestion->freeQuestionComments) ,
        'showUrl' => route('freeQuestion.show', $freeQuestion->id) , 
        'user' => [
            'id' => $freeQuestion->user->id,
            'name' => $freeQuestion->user->name,
            'profile_url' => route('profile.student.index', $freeQuestion->user->id),
            'score' => $freeQuestion->user->score,
        ],
        'tags' => $tags

    ];
  }

  public function fetchFreeQuestions()
  {
      $perPage = 5; // Number of comments per request
      $lastQuestionId = $this->request->lastQuestionId; // Get last loaded comment ID


      $freeQuestions = FreeQuestion::where('id', '>' , $lastQuestionId)
      ->with('user' , 'freeQuestionVotes' , 'freeQuestionComments')
      ->orderBy('id', 'desc')
      ->take($perPage)
      ->get();


      $freeQuestions = $freeQuestions->map(function($freeQuestion){            
          return $this->mapFreeQuestion($freeQuestion);
      });
      return response()->json($freeQuestions);

  }

  function makeSlug($string) {
    // Convert Persian spaces to hyphens
    $string = str_replace([' ', '‌'], '-', $string); 

    // Remove special characters (except hyphens)
    $string = preg_replace('/[^\p{L}\p{N}-]/u', '', $string);

    // Convert to lowercase
    return mb_strtolower($string, 'UTF-8');
  }


  public function updateUserBadge($user, $tagsId, $score)
  {     
    $data = [];
    foreach ($tagsId as $tagId) {
        $data = $data +  $this->CreateUserBadgeSingle($user , $tagId, $score);
    }
    $user->freeBadges()->syncWithoutDetaching($data);

  }

  public function CreateUserBadgeSingle($user, $tagId, $score)
  {
    $userBadge = $user->freeBadges->where('id', $tagId)->first();

    if(is_null($userBadge))
    {
      $newScore = $score;
    }
    else
    {
      $newScore = $userBadge->pivot->score + $score;
    }

    $freeTagValue = [
        'bronz1' =>     1000,
        'bronz2' =>     2000,
        'bronz3' =>     4000,
        'silver1' =>    8000,
        'silver2' =>    16000,
        'silver3' =>    32000,
        'gold1'  =>     640000,
        'gold2' =>      128000,
        'gold3' =>      256000,
        'platinum1' =>  512000,
        'platinum2' =>  1024000,
        'platinum3' =>  2048000,
        'dimond1' =>    4096000,
        'dimond2' =>    8192000,
        'dimond3' =>    16384000,
        'legendary1' => 32768000,
        'legendary2' => 65536000,
        'legendary3' => 131072000
    ];
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
      if($newScore > $freeTagValue[$badgeName])
      {
        $newBadge = $badgeName;
      }
    }

    return [$tagId => ['score' => $newScore, 'badge' => $newBadge]];
  }

  public function mapComment($comment){
    $canMarkAsBest = false;
    if( $comment->user_id !== auth()->user()->id)
    {
        $canMarkAsBest = true;
    }       
   
    return [
        'id' => $comment->id,
        'score' => $comment->score,
        'body' => $comment->body,
        'canMarkAsBest' => $canMarkAsBest , 
        'best_reply_id' => $comment->freeQuestion->best_reply_id ,             
        'user' => [
            'id' => $comment->user->id,
            'name' => $comment->user->name,
            'profile_url' => route('profile.student.index', $comment->user->id)
        ],
        'parent' => [
            'id' =>  $comment->parent ? $comment->parent->id : null,
            'user_id' =>  $comment->parent ? $comment->parent->user->id : null,
            'profile_url' => $comment->parent ? route('profile.student.index', $comment->parent->user->id) : null,
            'user_name' => $comment->parent ? $comment->parent->user->name : null,
            'body' => $comment->parent ? Str::limit($comment->parent->body, 50) : null
          ],
        ];
  }

  public function fetchComments()
  {
      $perPage = 5; // Number of comments per request
      $freeQuestionId = $this->request->freeQuestionId;
      $lastCommentId = $this->request->last_comment_id; // Get last loaded comment ID

      $query = FreeQuestionComment::where('free_question_id', $freeQuestionId)
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


}