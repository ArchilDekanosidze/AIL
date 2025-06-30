<table class="table table-bordered bg-white rounded">
    <thead class="thead-light">
        <tr>
            <th>تصویر</th>
            <th>کاربر</th>
            <th>نقش</th>
            <th>وضعیت</th>
            <th style="width: 300px;">عمل</th>
        </tr>
    </thead>
    <tbody>
        @forelse($participants as $participant)
            <tr>
                <td>
                    <img src="{{ asset($participant->user->avatar ?? '/images/Site/default-avatar.png') }}" 
                        alt="{{ $participant->user->name }} avatar" 
                        class="rounded-circle" 
                        width="40" 
                        height="40">
                </td>
                <td>{{ $participant->user->name }}</td>
                <td>{{ ucfirst($participant->persianRole) }}</td>
                <td>
                    @if ($participant->is_banned)
                        <span class="badge badge-danger">مسدود</span>
                    @elseif ($participant->is_muted)
                        <span class="badge badge-warning">ساکت</span>
                    @else
                        <span class="badge badge-success">فعال</span>
                    @endif
                </td>
                <td>
                    {{-- Promote / Demote --}}
                    @if ($canPromote && $participant->role === 'member')
                        <form method="POST" action="{{ route('chat.participants.promote', [$conversation->id, $participant->user_id]) }}" class="d-inline action-form">
                            @csrf
                            <button class="btn btn-sm btn-success">ارتقا به ادمین</button>
                        </form>
                    @elseif ($canDemote && $participant->role === 'admin')
                        <form method="POST" action="{{ route('chat.participants.demote', [$conversation->id, $participant->user_id]) }}" class="d-inline action-form">
                            @csrf
                            <button class="btn btn-sm btn-outline-secondary">حذف از ادمین ها</button>
                        </form>
                    @endif

                    {{-- Ban / Unban --}}
                    @if (!$participant->is_banned)
                        <form method="POST" action="{{ route('chat.participants.ban', [$conversation->id, $participant->user_id]) }}" class="d-inline action-form">
                            @csrf
                            <button class="btn btn-sm btn-danger">مسدود سازی</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('chat.participants.unban', [$conversation->id, $participant->user_id]) }}" class="d-inline action-form">
                            @csrf
                            <button class="btn btn-sm btn-outline-danger">حذف مسدودیت</button>
                        </form>
                    @endif

                    {{-- Mute / Unmute --}}
                    @if (!$participant->is_muted)
                        <form method="POST" action="{{ route('chat.participants.mute', [$conversation->id, $participant->user_id]) }}" class="d-inline action-form">
                            @csrf
                            <button class="btn btn-sm btn-warning">ساکت کردن</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('chat.participants.unmute', [$conversation->id, $participant->user_id]) }}" class="d-inline action-form">
                            @csrf
                            <button class="btn btn-sm btn-outline-warning">حذف سکوت</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center text-muted">هیچ کاربری پیدا نشد</td>
            </tr>
        @endforelse
    </tbody>
</table>
