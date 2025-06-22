<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Chat\MessageAttachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AttachmentController extends Controller
{
    /**
     * Download an attachment securely.
     */
    public function download($id) 
    {
        $attachment = MessageAttachment::with('message.conversation')->findOrFail($id);

        // Ensure the authenticated user is part of the conversation
        $isParticipant = $attachment->message->conversation
            ->participants()
            ->where('user_id', Auth::id())
            ->exists();

        if (!$isParticipant) {
            abort(403, 'شما اجازه دانلود این فایل را ندارید.');
        }

        return Storage::disk('private')->download($attachment->file_path);
    }

    /**
     * Delete an attachment (only owner or admin).
     */
    public function destroy($id)
    {
        $attachment = MessageAttachment::with('message')->findOrFail($id);

        $user = Auth::user();

        if ($attachment->message->user_id !== $user->id && !$user->is_admin) {
            abort(403, 'شما اجازه حذف این فایل را ندارید.');
        }

        // Delete file and record
        Storage::disk('private')->delete($attachment->file_path);
        $attachment->delete();

        return back()->with('success', 'فایل با موفقیت حذف شد.');
    }


    // ... (existing methods) ...

    /**
     * Serve a private attachment for display (images/videos).
     * This acts as a secure proxy.
     *
     * @param  \App\Models\Chat\MessageAttachment  $attachment
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function serveAttachment(MessageAttachment $attachment)
    {
        // 1. Authorize: Check if the current user is part of the conversation
        // that this attachment belongs to.
        $conversation = $attachment->message->conversation;

        if (!$conversation->participants->contains('user_id', Auth::id())) {
            return response()->json(['message' => 'Unauthorized to view this attachment.'], 403);
        }

        // 2. Ensure the file exists on the private disk
        if (!Storage::disk('private')->exists($attachment->file_path)) {
            return response()->json(['message' => 'Attachment file not found.'], 404);
        }

        // 3. Get the file content
        $fileContents = Storage::disk('private')->get($attachment->file_path);

        // 4. Return the file with appropriate headers for inline display
        // 'Content-Disposition: inline' tells the browser to display it if possible.
        return response($fileContents, 200)
            ->header('Content-Type', $attachment->mime_type)
            ->header('Content-Disposition', 'inline; filename="' . $attachment->file_name . '"');
    }

    // ... (rest of your controller) ...
}
