<?php

namespace App\Http\Controllers\Chat;

use App\Models\User;
use App\Models\Chat\Message;
use Illuminate\Http\Request;
use App\Models\Chat\Reaction;
use App\Models\Chat\Conversation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Chat\MessageAttachment;
use Illuminate\Support\Facades\Storage;
use App\Models\Chat\ConversationParticipant;

class ChatController extends Controller
{
    public function index()   // done
    {
        $user = Auth::user();

        // Eager load lastMessage for performance
        $conversations = $user->conversations()
            ->with(['lastMessage', 'participants.user'])
            ->get();

        return view('chat.home.index', compact('conversations'));
    }

    public function create() //done
    {
        return view('chat.home.create');
    }

    public function searchUsers(Request $request)  //done
    {
        $term = $request->get('q');
        return User::where('name', 'like', '%' . $term . '%')
                ->take(10)
                ->get(['id', 'name', 'avatar']); // assuming you have avatar
    }

    public function startConversation(Request $request)  //done
    {
        $authUser = auth()->user();
        $targetUserId = $request->input('user_id');

        // Check if a conversation already exists between these two users
        $conversation = Conversation::whereHas('participants', function ($q) use ($authUser) {
            $q->where('user_id', $authUser->id);
        })
        ->whereHas('participants', function ($q) use ($targetUserId) {
            $q->where('user_id', $targetUserId);
        })
        ->has('participants', '=', 2) // Make sure it's only a 2-person chat
        ->first();

        if (!$conversation) {
            // Create new conversation
            $conversation = Conversation::create([
                'title' => null, // or auto generate title
                'type' => 'simple'
            ]);
            $conversation->participants()->create([
                'user_id' => $authUser->id,
            ]);
            $conversation->participants()->create([
                'user_id' => $targetUserId,
            ]);
        }

        return response()->json([
            'conversation_id' => $conversation->id,
            'redirect_url' => route('chat.messages.index', $conversation->id)
        ]);
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
