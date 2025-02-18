<?php
namespace App\Services\Quiz\Trait;

use App\Models\CategoryQuestion;

trait QuizTrait
{
    public function questionAncestorsAndSelfId($question)
    {
        $categoriesId = CategoryQuestion::with('ancestors')->find($question->category_question_id)->ancestors->pluck('id');
        $categoriesId->shift();
        $categoriesId[] = $question->category_question_id;
        return $categoriesId;
    }
}