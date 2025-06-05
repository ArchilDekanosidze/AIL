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
}
