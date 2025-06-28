@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/profile/student/profile.css')}}">
@endsection
@section('content')
<div class="userProfile main-body"> 

    پروفایل    {{ $user->name }}


    @php
        use App\Models\Chat\Conversation;

        $authUser = auth()->user();
        $existingConversation = Conversation::where('type', 'private')
            ->whereHas('participants', fn($q) => $q->where('user_id', $authUser->id))
            ->whereHas('participants', fn($q) => $q->where('user_id', $user->id))
            ->has('participants', '=', 2)
            ->first();

        $isBanned = false;

        if ($existingConversation) {
            $targetParticipant = $existingConversation->participants()->where('user_id', $user->id)->first();
            $isBanned = $targetParticipant && $targetParticipant->is_banned;
        }
    @endphp

    @if (auth()->id() !== $user->id)
        <form action="{{ route('chat.toggle-ban-user') }}" method="POST" onsubmit="return confirm('آیا مطمئن هستید؟');">
            @csrf
            <input type="hidden" name="user_id" value="{{ $user->id }}">
            <button type="submit" class="btn-red">
                {{ $isBanned ? 'رفع مسدودی این کاربر' : 'مسدود کردن این کاربر' }}
            </button>
        </form>
    @endif



</div>
@endsection







@section('scripts')
    <script>
        $(document).ready(function() {

        });
    </script>
@endsection