<?php

namespace App\Models\Chat;

use App\Models\User;
use App\Models\Chat\Message;
use Illuminate\Database\Eloquent\Model;
use App\Models\Chat\ConversationParticipant;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conversation extends Model
{
    use HasFactory;
    
    protected $table = 'chat_conversations';

    protected $fillable = ['type', 'title', 'created_by', 'is_private', 'slug', 'owner_id', 'bio'];

    public function participants()
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }


        // --- Helper Methods for Authorization (updated for null user) ---

    /**
     * Get the participant record for a specific user in this conversation.
     *
     * @param User|null $user // Allow user to be null for guest scenarios
     * @return ConversationParticipant|null
     */
    public function getParticipant(?User $user): ?ConversationParticipant
    {
        if (!$user) {
            return null; // No user provided (e.g., guest), so no participant record
        }
        return $this->participants()->where('user_id', $user->id)->first();
    }

    /**
     * Check if a user is a member of this conversation (any role).
     *
     * @param User|null $user
     * @return bool
     */
    public function isMember(?User $user): bool
    {
        if (!$user) return false; // A guest is not considered a "member" in the participation sense
        return (bool)$this->getParticipant($user);
    }

    /**
     * Check if a user has a specific role in this conversation.
     *
     * @param User|null $user
     * @param string $role The role to check ('member', 'admin', 'super_admin')
     * @return bool
     */
    public function userHasRole(?User $user, string $role): bool
    {
        if (!$user) return false; // Guests don't have roles
        $participant = $this->getParticipant($user);
        return $participant && $participant->role === $role;
    }

    /**
     * Check if a user is a super admin of this conversation.
     *
     * @param User|null $user
     * @return bool
     */
    public function isSuperAdmin(?User $user): bool
    {
        return $this->userHasRole($user, 'super_admin');
    }

    /**
     * Check if a user is an admin (including super admin) of this conversation.
     *
     * @param User|null $user
     * @return bool
     */
    public function isAdmin(?User $user): bool
    {
        if (!$user) return false; // Guests are never admins
        $participant = $this->getParticipant($user);
        return $participant && in_array($participant->role, ['admin', 'super_admin']);
    }


    public function getPersianTypeAttribute()
    {
        return match ($this->type) {
            'channel' => 'کانال',
            'group'   => 'گروه',
            'private' => 'خصوصی',
            default   => 'نامشخص',
        };
    }
}
