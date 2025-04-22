<?php

use App\Models\CategoryQuestion;
use App\Services\CategoryQuestion\CategoriesQuestionService;

if(!function_exists('userCategoryStatus'))
{
    function userCategoryStatus($categoryId)
    {
        return app(CategoriesQuestionService::class)->userCategoryStatus($categoryId);
    }
}


