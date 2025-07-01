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

class UpdateFreeQuestionService
{
    use ActorTrait, FreeQuestionTrait;


    public function __construct(Request $request)
    {

        $this->request = $request;

    }

    public function updateQuestion()
    {
        $id = $this->request->route('question'); // or use $this->request->question
        $question = FreeQuestion::findOrFail($id);

        abort_if(auth()->id() !== $question->user_id, 403);

        $head = $this->request->freeQuestion_head;
        $body = $this->request->freeQuestion_body;
        $selectedTags = $this->request->selectedTags;

        if (is_null($head)) {
            return ['error' => "عنوان نباید خالی باشد"];
        }

        if (is_null($selectedTags) || count($selectedTags) === 0) {
            return ['error' => "حداقل یک تگ باید انتخاب کنید"];
        }

        if (count($selectedTags) > 5) {
            return ['error' => "بیشتر از ۵ تگ نمی‌توانید انتخاب کنید"];
        }

        $question->head = $head;
        $question->body = $body;
        $question->edited_at = now();
        $question->save();

        $selectedTagIds = collect($selectedTags)->map(function ($name) {
            return FreeTag::firstOrCreate([
                'name' => $name,
                'slug' => $this->makeSlug($name)
            ])->id;
        });

        $question->freeTags()->sync($selectedTagIds);

        $question = $this->mapFreeQuestion($question);

        return [
            'successMessages' => 'سوال با موفقیت بروزرسانی شد',
            'freeQuestion' => $question
        ];
    }



   

}