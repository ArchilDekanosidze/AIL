<?php
namespace App\Services\FreeQuestion;

use Illuminate\Http\Request;
use App\Services\Comment\Traits\CommentTrait;
use App\Services\Comment\SubService\VoteService;
use App\Services\Comment\SubService\BestReplyService;
use App\Services\Comment\SubService\NewCommentService;
use App\Services\FreeQuestion\Traits\FreeQuestionTrait;
use App\Services\Comment\Traits\ActorCommentServiceTrait;
use App\Services\FreeQuestion\SubService\NewFreeQuestionService;
use App\Services\FreeQuestion\SubService\FreeQuestionVoteService;
use App\Services\FreeQuestion\SubService\UpdateFreeQuestionService;
use App\Services\FreeQuestion\Traits\ActorFreeQuestionServiceTrait;
use App\Services\FreeQuestion\SubService\FreeQuestionBestReplyService;
use App\Services\FreeQuestion\SubService\NewFreeQuestionCommentService;
use App\Services\FreeQuestion\SubService\FreeQuestionCommentVoteService;
use App\Services\FreeQuestion\SubService\UpdateFreeQuestionCommentService;

class FreeQuestionService
{   use FreeQuestionTrait, ActorFreeQuestionServiceTrait; 
    private $newFreeQuestionService;
    private $updateFreeQuestionService;
    private $freeQuestionVoteService;
    private $newFreeQuestionCommentService;
    private $updateFreeQuestionCommentService;
    private $freeQuestionCommentVoteService;
    private $freeQuestionBestReplyService;



    public function __construct(Request $request,
        NewFreeQuestionService $newFreeQuestionService,
        UpdateFreeQuestionService $updateFreeQuestionService,
        FreeQuestionVoteService $freeQuestionVoteService,
        NewFreeQuestionCommentService $newFreeQuestionCommentService,
        UpdateFreeQuestionCommentService $updateFreeQuestionCommentService,
        FreeQuestionCommentVoteService $freeQuestionCommentVoteService,
        FreeQuestionBestReplyService $freeQuestionBestReplyService
        )
    {
        $this->newFreeQuestionService = $newFreeQuestionService;
        $this->updateFreeQuestionService = $updateFreeQuestionService;
        $this->freeQuestionVoteService = $freeQuestionVoteService;
        $this->newFreeQuestionCommentService = $newFreeQuestionCommentService;
        $this->updateFreeQuestionCommentService = $updateFreeQuestionCommentService;
        $this->freeQuestionCommentVoteService = $freeQuestionCommentVoteService;
        $this->freeQuestionBestReplyService = $freeQuestionBestReplyService;
        $this->request = $request;
    }

    public function newQuestion()
    {
        return $this->newFreeQuestionService->newQuestion();
    }

    public function updateQuestion()
    {
        return $this->updateFreeQuestionService->updateQuestion();
    }

    

    public function freeQuestionVote()
    {
        return $this->freeQuestionVoteService->vote();
    }


    public function newComment()
    {
        return $this->newFreeQuestionCommentService->newComment();
    }

    public function updateComment()
    {
        return $this->updateFreeQuestionCommentService->updateComment();
    }

    public function freeQuestionCommentVote()
    {
        return $this->freeQuestionCommentVoteService->vote();
    }

    public function setBestReply()
    {
        return $this->freeQuestionBestReplyService->setBestReply();
    }



}