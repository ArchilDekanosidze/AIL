<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Chat\Conversation;
use App\Models\Chat\Message;
use App\Models\Chat\MessageAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    // Show all messages in a conversation
    public function index($conversationId)
    {
        $conversation = Conversation::with('participants')->findOrFail($conversationId);
        $this->authorize('view', $conversation);

        $messages = Message::with('user', 'attachments', 'parent')
            ->where('conversation_id', $conversationId)
            ->orderBy('created_at')
            ->get();

        return view('chat.messages.index', compact('conversation', 'messages'));
    }

    // Store a new message
    public function store(Request $request, $conversationId)
    {
        $request->validate([
            'content' => 'nullable|string|max:5000',
            'parent_id' => 'nullable|exists:messages,id',
            'attachments.*' => 'file|max:10240', // Max 10MB per file
        ]);

        $conversation = Conversation::findOrFail($conversationId);
        $this->authorize('view', $conversation);

        // Save the message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
            'parent_id' => $request->input('parent_id'),
        ]);

        // Save attachments (if any)
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('chat/attachments', 'private');
                MessageAttachment::create([
                    'message_id' => $message->id,
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                ]);
            }
        }

        return redirect()->route('chat.messages.index', $conversation->id)
            ->with('success', 'پیام با موفقیت ارسال شد.');
    }

    // Download attachment securely
    public function downloadAttachment($attachmentId)
    {
        $attachment = MessageAttachment::findOrFail($attachmentId);
        $this->authorize('view', $attachment->message->conversation);

        return Storage::disk('private')->download($attachment->file_path);
    }
}
