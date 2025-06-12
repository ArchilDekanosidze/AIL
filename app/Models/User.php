<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Quiz;
use App\Models\Vote;
use App\Models\Jozve;
use App\Models\Comment;
use App\Models\FreeTag;
use App\Models\FreeFile;
use App\Models\Chat\Message;
use App\Models\FreeQuestion;
use Illuminate\Http\Request;
use App\Models\Chat\Reaction;
use App\Mail\VerificationEmail;
use App\Mail\ResetPasswordEmail;
use App\Models\CategoryQuestion;
use App\Models\Chat\Conversation;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Jobs\Notification\Email\SendEmail;
use App\Services\Auth\Traits\HasTwoFactor;
use App\Services\Auth\Traits\MustVerifyEmail;
use App\Services\Auth\Traits\MustVerifyMobile;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Services\CategoryQuestion\CategoriesQuestionService;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasTwoFactor, MustVerifyMobile, MustVerifyEmail;

    const SCORE_COMMENT = 50;
    const SCORE_REPLY = 30;
    const SCORE_VOTE = 100;
    const SCORE_VOTEING = 10;
    const SCORE_SETBESTREPLY = 20;
    const SCORE_BESTREPLY = 500;
    const SCORE_INCHATPERMINUTES = 1;
    const SCORE_FREEQUESTION = 100;
    const SCORE_FREEQUESTION_COMMENT = 50;
    const SCORE_FREEQUESTION_COMMENT_REPLY = 30;
    const SCORE_FREE_VOTE = 80;
    const SCORE_FREE_VOTEING = 10;
    const SCORE_FREE_COMMENT_VOTE = 60;
    const SCORE_FREE_COMMENT_VOTEING = 10;
    const SCORE_FREE_SETBESTREPLY = 15;
    const SCORE_FREE_BESTREPLY = 400;





    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'mobile',
        'mobile_verified_at',
        'email',
        'email_verified_at',
        'avatar',
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
        'mobile_verified_at' => 'datetime',
    ];



    public function categoryQuestions()
    {
        return $this->belongsToMany(CategoryQuestion::class, "user_category_question")
        ->withPivot('id', 'is_active','level','target_level',  'number_to_change_level')
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


    public function questions() {
        return $this->hasMany(Question::class);
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    public function votes() {
        return $this->hasMany(Vote::class);
    }

    public function incrementScore($points) {
        $this->increment('score', $points);
    }

    public function badges()
    {
        return $this->belongsToMany(Tag::class, 'user_badge', 'user_id', 'tag_id')
                    ->withPivot('badge', 'score')
                    ->withTimestamps();
    }


    public function freeQuestions() {
        return $this->hasMany(FreeQuestion::class);
    }

    public function freeBadges()
    {
        return $this->belongsToMany(FreeTag::class, 'user_free_badge', 'user_id', 'free_tag_id')
                    ->withPivot('badge', 'score')
                    ->withTimestamps();
    }

    public function jozves()
    {
        return $this->hasMany(Jozve::class);
    }

    public function freeFiles()
    {
        return $this->hasMany(FreeFile::class);
    }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'chat_conversation_participants', 'user_id', 'conversation_id')
                    ->withPivot('role')
                    ->withTimestamps();
    }
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }
}
