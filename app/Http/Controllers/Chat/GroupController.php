<?php
namespace App\Http\Controllers\Chat;

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

        return view('chat.groups.create', compact('type'));
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
            'slug' => $request->slug ?: Str::slug($request->title),
            'owner_id' => auth()->user()->id
        ]);

        // Add current user as participant
        $conversation->participants()->create([
            'user_id' => auth()->id(),
            'role' => 'admin', // optional
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
}
