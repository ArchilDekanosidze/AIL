<?php
namespace App\Http\Controllers\Question;


use App\Models\User;
use App\Models\Comment;
use App\Models\Question;
use Faker\Factory as faker;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CategoryQuestion;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\Tag;


class BestReplyController extends Controller
{
    public function setBestReply(Request $request)
    {
        $commentId = $request->commentId;
        $isBest = $request->isBest;

        
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

            $this->updateUserBadge( $comment->user->id, $comment->question->tag_id, User::SCORE_BESTREPLY);
            $this->updateUserBadge( auth()->user()->id, $comment->question->tag_id, User::SCORE_SETBESTREPLY);

        }
        if(!is_null($comment->best_reply_id) &&  !is_null($bestReplyid))
        {
            $oldBestReply = Comment::find($comment->best_reply_id);
            $oldBestReply->user->incrementScore(-User::SCORE_BESTREPLY);
            $comment->user->incrementScore(User::SCORE_BESTREPLY);

            $this->updateUserBadge( $oldBestReply->user->id, $comment->question->tag_id, -User::SCORE_BESTREPLY);
            $this->updateUserBadge( $comment->user->id, $comment->question->tag_id, User::SCORE_BESTREPLY);



        }
        if(!is_null($comment->best_reply_id) &&  is_null($bestReplyid))
        {
            $comment->user->incrementScore(-User::SCORE_BESTREPLY);
            auth()->user()->incrementScore(-User::SCORE_SETBESTREPLY);

            $this->updateUserBadge( $comment->user->id, $comment->question->tag_id, -User::SCORE_BESTREPLY);
            $this->updateUserBadge( auth()->user()->id, $comment->question->tag_id, -User::SCORE_SETBESTREPLY);

        }
        
        
        DB::statement("
        UPDATE comments 
        SET best_reply_id = ? 
        WHERE original_id = ?
        ", [$bestReplyid , $comment->original_id]);

        return ['success' => 'ثبت بهترین نظر شما با موفقیت انجام شد'];

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
