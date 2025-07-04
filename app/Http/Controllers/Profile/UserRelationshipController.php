<?php
namespace App\Http\Controllers\Profile;



use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile\UserRelationship;
use App\Models\Profile\UserRelationshipRequest;
use App\Http\Controllers\Controller;


class UserRelationshipController extends Controller
{
    // Send request to become someone's student/supervisor
    public function sendRequest(Request $request)
    {
        $request->validate([
            'target_user_id' => 'required|exists:users,id',
            'type' => 'required|in:supervisor,student',
        ]);

        $requesterId = Auth::id();
        $targetId = $request->target_user_id;
        $type = $request->type;

        if ($requesterId == $targetId) {
            return redirect()->back()->with('error', 'نمی‌توانید برای خودتان درخواست بفرستید.');
        }

        // Only check for active pending request
        $existing = UserRelationshipRequest::where('requester_id', $requesterId)
            ->where('target_id', $targetId)
            ->where('type', $type)
            ->whereIn('status', ['pending', 'accepted'])
            ->first();

        if ($existing) {
            if ($existing->status === 'pending') {
                return redirect()->back()->with('error', 'درخواست قبلاً ارسال شده است.');
            }

            return redirect()->back()->with('error', 'درخواستی با وضعیت مشابه موجود است.');
        }

        UserRelationshipRequest::create([
            'requester_id' => $requesterId,
            'target_id' => $targetId,
            'type' => $type,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'درخواست با موفقیت ارسال شد.');
    }



    // Accept relationship request
    public function accept(Request $request, $id)
    {
        $relationshipRequest = UserRelationshipRequest::findOrFail($id);

        if ($relationshipRequest->target_id !== Auth::id()) {
            abort(403, 'شما مجاز به انجام این عملیات نیستید.');
        }

        // Create actual relationship
        if ($relationshipRequest->type === 'supervisor') {
            UserRelationship::create([
                'supervisor_id' => $relationshipRequest->requester_id,
                'student_id' => $relationshipRequest->target_id,
            ]);
        } else {
            UserRelationship::create([
                'supervisor_id' => $relationshipRequest->target_id,
                'student_id' => $relationshipRequest->requester_id,
            ]);
        }

        // Delete the original request
        $relationshipRequest->delete();

        return redirect()->back()->with('success', 'درخواست پذیرفته شد.');
    }


    // Decline relationship request
    public function decline(Request $request, $id)
    {
        $relationshipRequest = UserRelationshipRequest::findOrFail($id);

        if ($relationshipRequest->target_id !== Auth::id()) {
            abort(403, 'شما مجاز به انجام این عملیات نیستید.');
        }

        // Instead of marking as "rejected", just delete the request
        $relationshipRequest->delete();

        return redirect()->back()->with('success', 'درخواست رد شد.');
    }


    // Optional: Cancel a pending request
    public function cancel(Request $request, $id)
    {
        $relationshipRequest = UserRelationshipRequest::findOrFail($id);

        if ($relationshipRequest->requester_id !== Auth::id()) {
            abort(403, 'شما مجاز به انجام این عملیات نیستید.');
        }

        $relationshipRequest->delete();

        return redirect()->back()->with('success', 'درخواست حذف شد.');
    }

    public function removeRelationship(Request $request, $id)
    {
        $relationship = UserRelationship::findOrFail($id);

        $authId = Auth::id();

        if ($relationship->supervisor_id !== $authId && $relationship->student_id !== $authId) {
            abort(403, 'شما مجاز به انجام این عملیات نیستید.');
        }

        $relationship->delete();

        return redirect()->back()->with('success', 'رابطه با موفقیت حذف شد.');
    }

}
