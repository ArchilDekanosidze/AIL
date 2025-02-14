<?php

namespace App\Services\CategoryQuestion;

use App\Models\CategoryQuestion;





class CategoriesQuestionService
{
    public function getDirectcats(CategoryQuestion $category)
    {
        $currentCategory = $category;
        $directCats =  $category->children()->get();
        return $directCats ;
    }

    public function getAncestor(CategoryQuestion $category)
    {
        $currentCategory = $category;
        $ancestor = $currentCategory->ancestors()->get();
        return $ancestor;
    }
}
