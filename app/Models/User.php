<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\CategoryQuestion;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'mobile',
        'mobile_verified_at',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function categoryQuestions()
    {
        return $this->belongsToMany(CategoryQuestion::class, "user_category_question");
    }

    public function userCategoryStatus($categoryId)
    {
        $category = CategoryQuestion::find($categoryId);
        $allcategoriesId = $category->getAllSubcatWithSelf();
        $userSelectedCategoryIds = $this->categoryQuestions()->pluck('category_question_id')->toArray();
        $selectedCategoriesId = array_intersect($allcategoriesId, $userSelectedCategoryIds);
        if(empty(array_diff($allcategoriesId, $selectedCategoriesId)))
        {
            $result = "all";
        }
        elseif (empty($selectedCategoriesId)) {
            $result = "none";
        }
        else
        {
            $result = "some";
        }
        return $result;
    }

    public function UserCheckedCategory()
    {
        $user = $this;
        $selectedCategories = $user->categoryQuestions;
        $ancestorCategories = CategoryQuestion::whereHas('descendants', function ($query) use($selectedCategories){
            $query->whereIn('id', $selectedCategories->pluck('id'));
        })->get();
        $categories = $ancestorCategories->merge($selectedCategories)->unique('id')->sortBy('lft');
        return $categories;
    }

    
}
