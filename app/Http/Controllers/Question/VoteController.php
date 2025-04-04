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
use App\Models\Tag;


class VoteController extends Controller
{
    public function vote(Request $request)
    {
        $commentId = $request->commentId;
        $voteType = $request->voteType == 'up' ? 1 : -1;;
        $comment = Comment::find($commentId);
        if ($comment->user_id == auth()->id()) {
            return response()->json(['errorSelfvoting' => 'شما نمی توانید به خودتون رای بدهید']);
        }
        $existingVote = $comment->votes()->where('user_id', auth()->id())->first();
        if($existingVote)
        {
            if ($existingVote->value == $voteType) {
                $existingVote->delete();
                $comment->decrement('score', $voteType);
                $comment->user->incrementScore(-$voteType * User::SCORE_VOTE); // Adjust user reputation
                auth()->user()->incrementScore(-User::SCORE_VOTEING); // Adjust user reputation

                $this->updateUserBadge($comment->user->id, $comment->question->tag_id, -$voteType * User::SCORE_VOTE);
                $this->updateUserBadge(auth()->user()->id, $comment->question->tag_id, -User::SCORE_VOTEING);

            }
            else
            {
                $existingVote->update(['value' => $voteType]);
                $comment->increment('score', 2 * $voteType); // Remove old vote and apply new one
                $comment->user->incrementScore(2 * $voteType * User::SCORE_VOTE);
                $this->updateUserBadge($comment->user->id, $comment->question->tag_id, 2 * $voteType * User::SCORE_VOTE);

            }
            return ['success' => 'رای شما با موفقیت به روز رسانی شد', 'vote' => $comment->score];
        }
        $comment->votes()->create([
            'user_id' => auth()->id(),
            'question_id' => $comment->question_id,
            'value' => $voteType,
        ]);

        auth()->user()->incrementScore(User::SCORE_VOTEING);
        $this->updateUserBadge( auth()->user()->id, $comment->question->tag_id,  User::SCORE_VOTEING);


        $comment->increment('score', $voteType); // Add vote to total score
        $comment->user->incrementScore($voteType * User::SCORE_VOTE);
        $this->updateUserBadge($comment->user->id, $comment->question->tag_id, $voteType * User::SCORE_VOTE);

        return ['success' => 'رای شما با موفقیت ثبت شد', 'vote' => $comment->score];

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
