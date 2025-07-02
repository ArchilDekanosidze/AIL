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


if (!function_exists('badgeLabel')) {
    function badgeLabel($badgeCode)
    {
        return match ($badgeCode) {
            'bronz1' => 'برنز ۱',
            'bronz2' => 'برنز ۲',
            'bronz3' => 'برنز ۳',
            'silver1' => 'نقره‌ای ۱',
            'silver2' => 'نقره‌ای ۲',
            'silver3' => 'نقره‌ای ۳',
            'gold1' => 'طلایی ۱',
            'gold2' => 'طلایی ۲',
            'gold3' => 'طلایی ۳',
            'platinum1' => 'پلاتینیوم ۱',
            'platinum2' => 'پلاتینیوم ۲',
            'platinum3' => 'پلاتینیوم ۳',
            'dimond1' => 'الماس ۱',
            'dimond2' => 'الماس ۲',
            'dimond3' => 'الماس ۳',
            'legendary1' => 'افسانه‌ای ۱',
            'legendary2' => 'افسانه‌ای ۲',
            'legendary3' => 'افسانه‌ای ۳',
            default => $badgeCode,
        };
    }
}

