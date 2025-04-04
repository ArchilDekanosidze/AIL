<?php
namespace App\Services\Comment;

use Illuminate\Http\Request;
use App\Services\Comment\Traits\CommentTrait;
use App\Services\Comment\SubService\VoteService;
use App\Services\Comment\SubService\BestReplyService;
use App\Services\Comment\SubService\NewCommentService;
use App\Services\Comment\Traits\ActorCommentServiceTrait;

class CommentService
{   use CommentTrait, ActorCommentServiceTrait; 
    private $newCommentService;
    private $voteService;
    private $bestReplyService;



    public function __construct(Request $request,
        NewCommentService $newCommentService, 
        VoteService $voteService,
        BestReplyService $bestReplyService )
    {
        $this->newCommentService = $newCommentService;
        $this->voteService = $voteService;
        $this->bestReplyService = $bestReplyService;
        $this->request = $request;
    }

    public function newComment()
    {
        return $this->newCommentService->newComment();
    }

    public function vote()
    {
        return $this->voteService->vote();
    }

    public function setBestReply()
    {
        return $this->bestReplyService->setBestReply();
    }
}