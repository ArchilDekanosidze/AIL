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
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Services\Comment\CommentService;


class BestReplyController extends Controller
{
    private $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;

    }
    
    public function setBestReply(Request $request)
    {      
      return $this->commentService->setBestReply();
    }

}
