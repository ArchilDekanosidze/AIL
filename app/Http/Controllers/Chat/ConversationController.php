<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat\Conversation;
use App\Models\Chat\ConversationParticipant;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    // Show list of conversations the user is part of
    public function index()
    {
        $user = Auth::user();
        $conversations = Conversation::whereHas('participants', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->latest()->get();

        return view('chat.conversations.index', compact('conversations'));
    }

    // Show form to create a conversation
    public function create()
    {
        return view('chat.conversations.create');
    }

    // Store a new conversation (group, channel, or private)
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:private,group,channel',
            'participant_ids' => 'array',
            'participant_ids.*' => 'exists:users,id',
        ]);

        $conversation = Conversation::create([
            'title' => $request->title,
            'type' => $request->type,
            'created_by' => Auth::id(),
        ]);

        // Add current user
        ConversationParticipant::create([
            'conversation_id' => $conversation->id,
            'user_id' => Auth::id(),
            'role' => 'admin',
        ]);

        // Add other participants if provided (for group/channel)
        if ($request->filled('participant_ids')) {
            foreach ($request->participant_ids as $userId) {
                ConversationParticipant::firstOrCreate([
                    'conversation_id' => $conversation->id,
                    'user_id' => $userId,
                ]);
            }
        }

        return redirect()->route('chat.conversations.index')->with('success', 'Conversation created.');
    }

    // Show a specific conversation
    public function show($id)
    {
        $conversation = Conversation::with('participants.user')->findOrFail($id);

        // Check if current user is part of it
        if (!$conversation->participants->contains('user_id', Auth::id())) {
            abort(403);
        }

        return view('chat.conversations.show', compact('conversation'));
    }

    // Show form to edit a conversation (only for admin/creator)
    public function edit($id)
    {
        $conversation = Conversation::findOrFail($id);

        if ($conversation->created_by !== Auth::id()) {
            abort(403);
        }

        return view('chat.conversations.edit', compact('conversation'));
    }

    // Update the conversation
    public function update(Request $request, $id)
    {
        $conversation = Conversation::findOrFail($id);

        if ($conversation->created_by !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $conversation->update([
            'title' => $request->title,
        ]);

        return redirect()->route('chat.conversations.index')->with('success', 'Conversation updated.');
    }

    // Delete the conversation
    public function destroy($id)
    {
        $conversation = Conversation::findOrFail($id);

        if ($conversation->created_by !== Auth::id()) {
            abort(403);
        }

        $conversation->delete();

        return redirect()->route('chat.conversations.index')->with('success', 'Conversation deleted.');
    }
}
