<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Quiz;
use Illuminate\Http\Request;
use App\Mail\VerificationEmail;
use App\Mail\ResetPasswordEmail;
use App\Models\CategoryQuestion;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Jobs\Notification\Email\SendEmail;
use App\Services\Auth\Traits\HasTwoFactor;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Services\CategoryQuestion\CategoriesQuestionService;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasTwoFactor;

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
        ->withPivot('is_active','level','target_level', 'history', 'number_to_change_level')
        ->where('user_category_question.is_active', true)
        ->withTimestamps();
    }

    public function quizzes()
    {
        return $this->belongsToMany(Quiz::class, "user_quiz");      
    }

    public function sendEmailVerificationNotification()
    {
        SendEmail::dispatch($this, new VerificationEmail($this));
    }

    public function sendPasswordResetNotification($token)
    {
        SendEmail::dispatch($this, new ResetPasswordEmail($this, $token));
    }

    public function hasEmail()
    {
        return $this->email;
    }

    public function hasMobile()
    {
        return $this->mobile;
    }

    public function isAdmin()
    {
        return $this->user_type;
    }

    
}
