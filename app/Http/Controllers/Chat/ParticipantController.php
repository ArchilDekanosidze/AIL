<?php
namespace App\Http\Controllers\Chat;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Chat\Conversation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Events\Chat\ParticipantStatusUpdated;

class ParticipantController extends Controller
{
    public function join(Request $request, Conversation $conversation)
    {
        $user = Auth::user();

        if (!$user) return redirect()->route('auth.login');

        // Prevent joining private conversations
        if ($conversation->type === 'private') {
            return redirect()->back()->with('error', 'Cannot join a private conversation.');
        }

        // Avoid duplicate joins
        if (!$conversation->participants->contains('user_id', $user->id)) {
            $conversation->participants()->create([
                'user_id' => $user->id,
                'role' => 'member', // default role
            ]);
        }

        return redirect()->back()->with('success', 'You joined the conversation!');
    }




public function manage(Request $request, Conversation $conversation)
{
    $user = Auth::user();

    $conversation->load(['participants.user']);

    $authParticipant = $conversation->participants->firstWhere('user_id', $user->id);

    if (!$authParticipant || !in_array($authParticipant->role, ['admin', 'super_admin'])) {
        abort(403, 'Unauthorized.');
    }

    $query = $conversation->participants()
        ->where('user_id', '!=', $user->id)
        ->with('user');

    if ($authParticipant->role === 'admin') {
        $query->where('role', 'member');
    }

    if ($search = $request->query('search')) {
        $query->whereHas('user', function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        });
    }

    // New filter logic:
    $filter = $request->query('filter');
    if ($filter === 'banned') {
        $query->where('is_banned', true);
    } elseif ($filter === 'muted') {
        $query->where('is_muted', true);
    }

    $participants = $query->get();

    

    if ($authParticipant->role === 'super_admin') {
        $participants = $participants->sortByDesc(fn($p) => $p->role === 'admin' ? 1 : 0)->values();
    }

    return view('chat.participants.manage', [
        'conversation' => $conversation,
        'participants' => $participants,
        'authParticipant' => $authParticipant,
        'canPromote' => $authParticipant->role === 'super_admin',
        'canDemote' => $authParticipant->role === 'super_admin',
        'canManageMuteBan' => in_array($authParticipant->role, ['admin', 'super_admin']),
    ]);
}





public function searchParticipants(Request $request, Conversation $conversation)
{
    $user = Auth::user();
    $authParticipant = $conversation->participants()->where('user_id', $user->id)->first();

    if (!$authParticipant || !in_array($authParticipant->role, ['admin', 'super_admin'])) {
        abort(403);
    }

    $search = $request->input('q', '');

    // Query participants in this conversation excluding current user
    $query = $conversation->participants()
        ->where('user_id', '!=', $user->id)
        ->whereHas('user', function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%");
        });

    // Role-based filter
    if ($authParticipant->role === 'admin') {
        $query->where('role', 'member');
    }

    $participants = $query->with('user')->get();

    $canPromote = $authParticipant->role === 'super_admin';
    $canDemote = $authParticipant->role === 'super_admin';

    return view('partials.manage-users-search-results', compact('conversation', 'participants', 'canPromote', 'canDemote'));
}







    public function promote(Conversation $conversation, User $user)
    {
        $this->authorizeSuperAdmin($conversation);

        $participant = $conversation->participants()->where('user_id', $user->id)->firstOrFail();
        $participant->update(['role' => 'admin']);

        return back()->with('success', 'User promoted to admin.');
    }

    public function demote(Conversation $conversation, User $user)
    {
        $this->authorizeSuperAdmin($conversation);

        $participant = $conversation->participants()->where('user_id', $user->id)->firstOrFail();
        $participant->update(['role' => 'member']);

        return back()->with('success', 'Admin demoted to member.');
    }

    public function mute(Conversation $conversation, User $user)
    {
        $this->authorizeAdminOrHigher($conversation);

        $participant = $conversation->participants()->where('user_id', $user->id)->firstOrFail();
        $participant->update(['is_muted' => true]);

        broadcast(new ParticipantStatusUpdated($conversation->id, $user->id, 'muted'));

        return back()->with('success', 'User muted.');
    }

    public function unmute(Conversation $conversation, User $user)
    {
        $this->authorizeAdminOrHigher($conversation);

        $participant = $conversation->participants()->where('user_id', $user->id)->firstOrFail();
        $participant->update(['is_muted' => false]);

        return back()->with('success', 'User unmuted.');
    }

    public function ban(Conversation $conversation, User $user)
    {
        $this->authorizeAdminOrHigher($conversation);

        $participant = $conversation->participants()->where('user_id', $user->id)->firstOrFail();
        $participant->update(['is_banned' => true]);


        broadcast(new ParticipantStatusUpdated($conversation->id, $user->id, 'banned'));


        return back()->with('success', 'User banned.');
    }

    public function unban(Conversation $conversation, User $user)
    {
        $this->authorizeAdminOrHigher($conversation);

        $participant = $conversation->participants()->where('user_id', $user->id)->firstOrFail();
        $participant->update(['is_banned' => false]);

        return back()->with('success', 'User unbanned.');
    }

    protected function authorizeSuperAdmin(Conversation $conversation)
    {
        $user = Auth::user();
        $participant = $conversation->participants()->where('user_id', $user->id)->first();

        if (!$participant || $participant->role !== 'super_admin') {
            abort(403);
        }
    }

    protected function authorizeAdminOrHigher(Conversation $conversation)
    {
        $user = Auth::user();
        $participant = $conversation->participants()->where('user_id', $user->id)->first();

        if (!$participant || !in_array($participant->role, ['admin', 'super_admin'])) {
            abort(403);
        }
    }

}
