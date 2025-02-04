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
        return $this->belongsToMany(CategoryQuestion::class, "user_category_question")
        ->withPivot('is_active','level','target_level', 'level_history', 'answer_history', 'number_to_change_level')
        ->where('user_category_question.is_active', true)
        ->withTimestamps();
    }

    public function add_category_to_user($categoryId)
    {
        $categoriesId = CategoryQuestion::descendantsAndSelf($categoryId)->pluck('id');
        
        $data = $categoriesId->mapWithKeys(function($id){
            return [$id => ['is_active' => true]];
        })->toArray();

        $this->categoryQuestions()->syncWithoutDetaching($data);
        
        return true;
    }
    public function remove_category_from_user($categoryId)
    {
        $categoriesId = CategoryQuestion::descendantsAndSelf($categoryId)->pluck('id');
       
        $data = $categoriesId->mapWithKeys(function($id){
            return [$id => ['is_active' => false]];
        })->toArray();

        $this->categoryQuestions()->syncWithoutDetaching($data);

        return true;
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

        // $selectedCategories = $user->categoryQuestions;
        // $ancestorCategories = CategoryQuestion::whereHas('descendants', function ($query) use($selectedCategories){
        //     $query->whereIn('id', $selectedCategories->pluck('id'));
        // })->get();
        
        // $categories = $ancestorCategories->merge($selectedCategories)->unique('id')->sortBy('lft');
        
        // $userId = $user->id;
        // $chosenCategoryQuestion = CategoryQuestion::whereHas('users', function ($query) use($userId){
        //         $query->where('user_id', $userId);
        //     })->with(["users" => function($query) use($userId){
        //         $query->where('user_id', $userId)->select('users.id')->withPivot('level');
        //     }])
        //     ->get();
        // dd($user->categoryQuestions()->get()->pluck('id'));
        // dd($chosenCategoryQuestion->first()->users()->first()->level);
        
        // $categories = $this->categoryQuestions()->get()->sortBy('lft');
        

        // return $categories;
    }

    
}
