<?php
namespace App\Http\Controllers\Chat;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Chat\Conversation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    // Step 1: Show form to create group/channel
    public function create(Request $request) 
    {
        $type = $request->query('type', 'group'); // default to group if not set

        if (!in_array($type, ['group', 'channel'])) {
            abort(404);
        }
        
        $persianType = "";
        if($type =='channel' )
        {
            $persianType = "کانال";
        }

        if($type =='group' )
        {
            $persianType = "گروه";
        }

        return view('chat.groups.create', compact('type', 'persianType'));
    }

    // Step 1: Store group/channel and redirect to Step 2
    public function store(Request $request)  
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:group,channel',
            'is_private' => 'required|boolean',
            'slug' => 'string|max:255|unique:chat_conversations,slug',
        ]);

        $conversation = Conversation::create([
            'title' => $request->title,
            'type' => $request->type, // "group" or "channel"
            'is_private' => $request->is_private,
            'slug' => $request->link ?: Str::slug($request->title),
            'owner_id' => auth()->user()->id
        ]);

        // Add current user as participant
        $conversation->participants()->create([
            'user_id' => auth()->id(),
            'role' => 'super_admin', // optional
        ]);

        return redirect()->route('chat.groups.add-users', $conversation->id);
    }

    // Step 2: Show user selection page
    public function addUsersForm(Conversation $conversation)  
    {
        // $this->authorize('update', $conversation); // optional if using policies
        return view('chat.groups.add-users', [
            'conversation' => $conversation,
        ]);
    }

    // Step 2: Store selected users
    public function addUsers(Request $request, Conversation $conversation)  
    {
        // Optional: Ensure only the conversation owner/admin can add users
        // $this->authorize('update', $conversation);

        $request->validate([
            // The frontend sends user_ids as a comma-separated string
            'user_ids' => 'nullable|string', // Changed to nullable string for scenarios where no new users are added
        ]);

        $selectedUserIds = [];
        if ($request->filled('user_ids')) {
            // Parse the comma-separated string into an array of integers
            $selectedUserIds = array_filter(array_map('intval', explode(',', $request->user_ids)));

            // Validate each ID individually to ensure they exist
            $validator = Validator::make(['user_ids' => $selectedUserIds], [
                'user_ids.*' => 'exists:users,id',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
        }

        // Get IDs of users already participating in this conversation
        // This is crucial to avoid creating duplicate ConversationParticipant records
        $existingParticipantUserIds = $conversation->participants()->pluck('user_id')->toArray();

        // Filter out users who are already participants
        $usersToAddToConversation = array_diff($selectedUserIds, $existingParticipantUserIds);

        $participantsData = [];
        foreach ($usersToAddToConversation as $userId) {
            $participantsData[] = [
                'user_id' => $userId,
                'role' => 'member', // Default role for newly added users
                'joined_at' => now(), // Set the joined timestamp
                'created_at' => now(), // For mass insertion
                'updated_at' => now(), // For mass insertion
            ];
        }

        if (!empty($participantsData)) {
            // Use createMany for efficient mass insertion
            $conversation->participants()->createMany($participantsData);
        }

        // Redirect to the conversation show page
        return redirect()->route('chat.messages.index', $conversation->id)
                         ->with('success', 'Members added successfully!');
    }


    public function info(Conversation $conversation)
    {
        // $this->authorize('view', $conversation); // Optional policy check

        $participant = $conversation->participants()->where('user_id', auth()->id())->first();
        $role = $participant?->role ?? 'guest';

        return view('chat.conversations.info', compact('conversation', 'role'));
    }

    public function updateInfo(Request $request, Conversation $conversation)
    {
        $participant = $conversation->participants()->where('user_id', auth()->id())->first();
        $role = $participant?->role ?? 'guest';

        if (!in_array($role, ['admin', 'super_admin'])) {
            abort(403);
        }

        $validated = $request->validate([
            'link' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'is_private' => 'nullable|boolean', // ✅ validate is_private
        ]);

        $conversation->update([
            'slug' => $validated['link'] ?? $conversation->slug,
            'bio' => $validated['bio'] ?? $conversation->bio,
            'is_private' => $validated['is_private'] ?? $conversation->is_private, // ✅ update is_private
        ]);

        return redirect()->back()->with('success', 'اطلاعات ' .$conversation->persianType .' با موفقیت به‌روزرسانی شد.');
    }

    public function searchUsersForm(Conversation $conversation)  
    {
        // $this->authorize('update', $conversation); // optional if using policies

        return view('chat.groups.add-users_afterCreatedChannel', [
            'conversation' => $conversation,
        ]);
    }

    public function searchUsers(Request $request)
    {
        $query = $request->input('q');
        $conversationId = $request->input('conversation_id'); // Pass this from JS

        $alreadyParticipantIds = [];

        if ($conversationId) {
            $conversation = Conversation::findOrFail($conversationId);
            $alreadyParticipantIds = $conversation->participants()->pluck('user_id')->toArray();
        }

        $users = User::query()
            ->where('name', 'like', "%{$query}%")
            ->whereNotIn('id', $alreadyParticipantIds)
            ->limit(10)
            ->get(['id', 'name', 'avatar']);

        return response()->json($users);
    }



}
