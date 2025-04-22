<?php
namespace App\Services\FreeQuestion\SubService;

use App\Models\User;
use App\Models\Comment;
use App\Models\FreeTag;
use App\Models\FreeQuestion;
use Illuminate\Http\Request;
use App\Services\Traits\ActorTrait;
use App\Services\Comment\Traits\CommentTrait;
use App\Services\FreeQuestion\Traits\FreeQuestionTrait;

class NewFreeQuestionService
{
    use ActorTrait, FreeQuestionTrait;


    public function __construct(Request $request)
    {

        $this->request = $request;

    }


    public function newQuestion()
    {
        $head = $this->request->freeQuestion_head;
        $body = $this->request->freeQuestion_body;
        $selectedTags = $this->request->selectedTags;
        if(is_null($head))
        {
            return  ['error' => "عنوان  نباید خالی باشد"];
        }
        if(is_null($selectedTags))
        {
            return  ['error' => "حداقل یک تگ باید انتخاب کنید"];
        }

        if(count($selectedTags)>5)
        {
            return  ['error' => "بیشتر از ۵ تگ نمی توانید انتخاب کنید"];
        }
        
        $freeQuestion = new FreeQuestion();
        $freeQuestion->user_id = auth()->user()->id;
        $freeQuestion->head = $head;
        $freeQuestion->body = $body;
        $freeQuestion->save();


        $selectedTagIds = collect($selectedTags)->map(function ($name) {
            return FreeTag::firstOrCreate(['name' => $name , 'slug' => $this->makeSlug($name)])->id;
        });
        
        $freeQuestion->freeTags()->sync($selectedTagIds);

        $freeQuestion->user->incrementScore(User::SCORE_FREEQUESTION);

        $this->updateUserBadge(auth()->user(), $freeQuestion->freeTags->pluck('id'), User::SCORE_FREEQUESTION);


        $freeQuestion = $this->mapFreeQuestion($freeQuestion);


        $successMessages = 'سوال شما با موفقیت  ثبت شد';
        return  ['successMessages' => $successMessages, 'freeQuestion' => $freeQuestion];
    }
   

}