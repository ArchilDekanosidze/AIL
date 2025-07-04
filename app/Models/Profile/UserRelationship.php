<?php
namespace App\Models\Profile;



use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserRelationship extends Model
{
    use HasFactory;

    protected $fillable = [
        'supervisor_id',
        'student_id',
    ];

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
