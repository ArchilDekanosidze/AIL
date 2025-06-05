<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Chat\Conversation;
use App\Models\Chat\ConversationParticipant;
use App\Models\Chat\Message;
use App\Models\Chat\MessageAttachment;
use App\Models\Chat\Reaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Eager load lastMessage for performance
        $conversations = $user->conversations()
            ->with(['lastMessage', 'participants.user'])
            ->get();

        return view('chat.index', compact('conversations'));
    }

    public function show($id)
    {
        $conversation = Conversation::with([
            'messages.user',
            'messages.attachments',
            'participants.user'
        ])->findOrFail($id);

        return view('chat.show', compact('conversation'));
    }

    public function storeMessage(Request $request, $conversationId)
    {
        $request->validate([
            'content' => 'nullable|string',
            'attachments.*' => 'file|max:20480|mimes:jpg,jpeg,png,mp4,mp3,wav,pdf,doc,docx,zip',
        ]);

        // Create the message
        $message = Message::create([
            'conversation_id' => $conversationId,
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
        ]);

        // Store attachments if any
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('chat_attachments', 'private');
                MessageAttachment::create([
                    'message_id' => $message->id,
                    'file_path' => $path,
                    'type' => $file->getClientMimeType(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'پیام ارسال شد');
    }

    public function react(Request $request, $messageId)
    {
        $request->validate([
            'reaction' => 'required|string|max:10',
        ]);

        Reaction::updateOrCreate(
            [
                'message_id' => $messageId,
                'user_id' => Auth::id(),
            ],
            [
                'reaction' => $request->reaction,
            ]
        );

        return response()->json(['status' => 'success']);
    }

    public function downloadAttachment($id)
    {
        $attachment = MessageAttachment::findOrFail($id);

        return Storage::disk('private')->download($attachment->file_path);
    }
}
