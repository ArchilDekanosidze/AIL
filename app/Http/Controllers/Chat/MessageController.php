<?php

namespace App\Http\Controllers\Chat;

use App\Models\Chat\Message;
use Illuminate\Http\Request;
use Hekmatinasser\Verta\Verta;
use App\Events\Chat\MessageSent;
use App\Models\Chat\Conversation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Chat\MessageAttachment;
use Illuminate\Support\Facades\Storage;


class MessageController extends Controller
{
    // Show all messages in a conversation
    public function index($conversationId)
    {
        $conversation = Conversation::with('participants')->findOrFail($conversationId);
        // $this->authorize('view', $conversation);


        return view('chat.messages.index', compact('conversation'));
        
    }

    public function getMessages(Request $request, Conversation $conversation) 
    {
        $beforeId = $request->query('before');

        $query = $conversation->messages()
            ->with(['sender', 'attachments'])
            ->orderByDesc('id')
            ->limit(10);

        if ($beforeId) {
            $query->where('id', '<', $beforeId);
        }

        $messages = $query->get()->reverse()->values();

        $transformed = $messages->map(function ($message) {
            return [
                'id' => $message->id,
                'sender' => [
                    'id' => $message->sender->id ?? null,
                    'name' => $message->sender->name ?? 'Unknown',
                ],
                'content' => $message->content,
                'created_at' => (new Verta($message->created_at))->formatDifference(), // ✅ Verta time ago
                'attachments' => $message->attachments->map(function ($att) {
                    return [
                        'id' => $att->id,
                        'file_path' => $att->file_path,
                        'download_url' => route('chat.attachments.download', $att->id),
                    ];
                }),
            ];
        });

        return response()->json($transformed);
    }

    // Store a new message
    public function store(Request $request, Conversation $conversation)  
    {
        $message = $conversation->messages()->create([
            'sender_id' => auth()->id(),
            'content' => $request->input('content'),
        ]);

        // Save attachments...
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('chat/attachments', 'private');
                $message->attachments()->create(['file_path' => $path]);
            }
        }

        $message->load('sender', 'attachments');

        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'id' => $message->id,
            'sender_name' => $message->sender->name,
            'content' => $message->content,
            'attachments' => $message->attachments->map(function ($att) {
                return [
                    'id' => $att->id,
                    'filename' => basename($att->file_path),
                    'download_url' => route('chat.attachments.download', $att->id),
                ];
            }),
            'created_at' => $message->created_at->diffForHumans(),
        ]);
    }
}
