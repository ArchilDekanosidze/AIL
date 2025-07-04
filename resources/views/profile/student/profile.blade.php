@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/profile/student/profile.css')}}">
@endsection
@section('content')
<div class="userProfile main-body"> 

    پروفایل    {{ $user->name }} ({{ number_format($user->score) }} امتیاز)


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
        <div class="profile-actions">
            <form action="{{ route('chat.start-private-from-profile') }}" method="POST" style="display: inline-block;">
                @csrf
                <input type="hidden" name="target_user_id" value="{{ $user->id }}">
                <button type="submit" class="btn btn-primary">شروع گفتگو</button>
            </form>

            <form action="{{ route('chat.toggle-ban-user') }}" method="POST" onsubmit="return confirm('آیا مطمئن هستید؟');" style="display: inline-block;">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <button type="submit" class="btn-red">
                    {{ $isBanned ? 'رفع مسدودی' : 'مسدود کردن' }}
                </button>
            </form>
        </div>
    @else
        <a href="{{ route('chat.saved-messages') }}" class="btn btn-secondary">ذخیره شده‌ها</a>
    @endif


    <div class="profile-avatar">
        <img src="{{ $user->avatar_url }}" alt="avatar" class="avatar-img">

        @if(auth()->id() === $user->id)
            <form method="POST" action="{{ route('profile.avatar.upload') }}" enctype="multipart/form-data">
                @csrf
                <input type="file" name="avatar" accept="image/*" onchange="this.form.submit()">
            </form>
        @endif
    </div>






    @if(auth()->id() !== $user->id)
        <div class="relationship-actions mt-4">
            @if($relationshipStatus['isSupervisor'] || $relationshipStatus['isStudent'])
                <form action="{{ route('supervising.remove', ['id' => $relationshipStatus['relationshipId']]) }}" method="POST">
                    @csrf @method('DELETE')
                    <button class="btn btn-warning">
                        {{ $relationshipStatus['isSupervisor'] ? 'لغو شاگردی این کاربر' : 'لغو استادی این کاربر' }}
                    </button>
                </form>

            @elseif($relationshipStatus['existingRequest'])
                @php
                    $request = $relationshipStatus['existingRequest'];
                    $isIncoming = $request->target_id === auth()->id(); // You received the request
                @endphp

                @if($isIncoming)
                    <form action="{{ route('supervising.accept', ['id' => $request->id]) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-success">پذیرفتن درخواست</button>
                    </form>

                    <form action="{{ route('supervising.decline', ['id' => $request->id]) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-danger">رد درخواست</button>
                    </form>
                @else
                    <form action="{{ route('supervising.cancel', ['id' => $request->id]) }}" method="POST">
                        @csrf @method('DELETE')
                        <button class="btn btn-secondary">لغو درخواست</button>
                    </form>
                @endif

            @else
                <form action="{{ route('supervising.send') }}" method="POST" class="d-inline-flex align-items-center gap-2">
                    @csrf
                    <input type="hidden" name="target_user_id" value="{{ $user->id }}">
                    <select name="type" class="form-select w-auto">
                        <option value="supervisor">درخواست استاد شدن</option>
                        <option value="student">درخواست شاگرد شدن</option>
                    </select>
                    <button type="submit" class="btn btn-primary">ارسال درخواست</button>
                </form>
            @endif
        </div>
    @endif


    @if(auth()->id() === $user->id && $incomingRequests && $incomingRequests->isNotEmpty())
        <div class="mt-4">
            <h4>درخواست‌های دریافتی</h4>
            @foreach($incomingRequests as $request)
                <div class="card p-2 my-2">
                    <p>
                        {{ $request->requester->name }} 
                        درخواست داده تا 
                        {{ $request->type === 'supervisor' ? 'استاد' : 'شاگرد' }}
                        شما شود.
                    </p>
                    <form action="{{ route('supervising.accept', $request->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-success btn-sm">پذیرفتن</button>
                    </form>
                    <form action="{{ route('supervising.decline', $request->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-danger btn-sm">رد</button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif



    @if(auth()->id() === $user->id)

        @if($mySupervisors && $mySupervisors->count())
            <h4 class="mt-4">اساتید من</h4>
            <ul>
                @foreach($mySupervisors as $relation)
                    <li>
                        <a href="{{ route('profile.student.index', $relation->supervisor->id) }}">
                            {{ $relation->supervisor->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif

        @if($myStudents && $myStudents->count())
            <h4 class="mt-4">شاگردان من</h4>
            <ul>
                @foreach($myStudents as $relation)
                    <li>
                        <a href="{{ route('profile.student.index', $relation->student->id) }}">
                            {{ $relation->student->name }}
                        </a>
                        <div class="mt-3">
                            <a href="{{ route('desktop.quizList', $relation->student->id) }}" class="btn btn-info">
                                📊 مشاهده آزمون‌های این شاگرد
                            </a>
                        </div>
                        <a href="{{ route('desktop.myProgress', $relation->student->id) }}" class="btn btn-info mt-2">📊 مشاهده پیشرفت شاگرد</a>
                    </li>
                @endforeach
            </ul>
        @endif

    @endif







    <h3 class="mt-4 mb-2">🏅 نشان‌ها</h3>
    @php
        $badgeIcons = [
            'bronz1' => '<svg viewBox="0 0 64 64" class="badge-icon" fill="#cd7f32" xmlns="http://www.w3.org/2000/svg"><circle cx="32" cy="32" r="28" /><text x="32" y="38" font-size="18" fill="#fff" text-anchor="middle" font-weight="bold">1</text></svg>',
            'bronz2' => '<svg viewBox="0 0 64 64" class="badge-icon" fill="#b5671e" xmlns="http://www.w3.org/2000/svg"><circle cx="32" cy="32" r="28" /><polygon points="20,50 44,50 32,30" fill="#a06020"/></svg>',
            'bronz3' => '<svg viewBox="0 0 64 64" class="badge-icon" fill="#a55d1e" xmlns="http://www.w3.org/2000/svg"><circle cx="32" cy="32" r="28" /><path d="M20 20 L44 20 L32 44 Z" fill="#854613"/></svg>',

            'silver1' => '<svg viewBox="0 0 64 64" class="badge-icon" fill="#c0c0c0" xmlns="http://www.w3.org/2000/svg"><circle cx="32" cy="32" r="28" /><text x="32" y="38" font-size="18" fill="#222" text-anchor="middle" font-weight="bold">1</text></svg>',
            'silver2' => '<svg viewBox="0 0 64 64" class="badge-icon" fill="#9ea0a2" xmlns="http://www.w3.org/2000/svg"><circle cx="32" cy="32" r="28" /><polygon points="20,50 44,50 32,30" fill="#7a7a7a"/></svg>',
            'silver3' => '<svg viewBox="0 0 64 64" class="badge-icon" fill="#828282" xmlns="http://www.w3.org/2000/svg"><circle cx="32" cy="32" r="28" /><path d="M20 20 L44 20 L32 44 Z" fill="#5d5d5d"/></svg>',

            'gold1' => '<svg viewBox="0 0 64 64" class="badge-icon" fill="#ffd700" xmlns="http://www.w3.org/2000/svg"><circle cx="32" cy="32" r="28" /><text x="32" y="38" font-size="18" fill="#bfa500" text-anchor="middle" font-weight="bold">1</text></svg>',
            'gold2' => '<svg viewBox="0 0 64 64" class="badge-icon" fill="#ffcc00" xmlns="http://www.w3.org/2000/svg"><circle cx="32" cy="32" r="28" /><polygon points="20,50 44,50 32,30" fill="#c9a000"/></svg>',
            'gold3' => '<svg viewBox="0 0 64 64" class="badge-icon" fill="#e6b800" xmlns="http://www.w3.org/2000/svg"><circle cx="32" cy="32" r="28" /><path d="M20 20 L44 20 L32 44 Z" fill="#b48b00"/></svg>',

            'platinum1' => '<svg viewBox="0 0 64 64" class="badge-icon" fill="#e5e4e2" xmlns="http://www.w3.org/2000/svg"><rect x="10" y="10" width="44" height="44" rx="8" ry="8" /></svg>',
            'platinum2' => '<svg viewBox="0 0 64 64" class="badge-icon" fill="#c4c4c4" xmlns="http://www.w3.org/2000/svg"><rect x="10" y="10" width="44" height="44" rx="12" ry="12" /></svg>',
            'platinum3' => '<svg viewBox="0 0 64 64" class="badge-icon" fill="#a9a9a9" xmlns="http://www.w3.org/2000/svg"><rect x="10" y="10" width="44" height="44" rx="16" ry="16" /></svg>',

            'dimond1' => '<svg viewBox="0 0 64 64" class="badge-icon" fill="#b9f2ff" xmlns="http://www.w3.org/2000/svg"><polygon points="32,6 50,30 32,58 14,30" /></svg>',
            'dimond2' => '<svg viewBox="0 0 64 64" class="badge-icon" fill="#96d9ff" xmlns="http://www.w3.org/2000/svg"><polygon points="32,4 52,32 32,60 12,32" /></svg>',
            'dimond3' => '<svg viewBox="0 0 64 64" class="badge-icon" fill="#74c1ff" xmlns="http://www.w3.org/2000/svg"><polygon points="32,2 54,34 32,62 10,34" /></svg>',

            'legendary1' => '<svg viewBox="0 0 64 64" class="badge-icon" fill="#ff4500" xmlns="http://www.w3.org/2000/svg"><path d="M32 4 L40 24 L60 24 L44 38 L50 58 L32 46 L14 58 L20 38 L4 24 L24 24 Z"/></svg>',
            'legendary2' => '<svg viewBox="0 0 64 64" class="badge-icon" fill="#ff3300" xmlns="http://www.w3.org/2000/svg"><path d="M32 2 L42 26 L62 26 L46 42 L52 62 L32 50 L12 62 L18 42 L2 26 L22 26 Z"/></svg>',
            'legendary3' => '<svg viewBox="0 0 64 64" class="badge-icon" fill="#ff1a00" xmlns="http://www.w3.org/2000/svg"><path d="M32 1 L44 28 L64 28 L48 44 L54 64 L32 52 L10 64 L16 44 L0 28 L20 28 Z"/></svg>',
        ];
    @endphp




    @if($user->freeBadges->isEmpty())
        <p class="text-gray-500">این کاربر هنوز نشانی دریافت نکرده است.</p>
    @else
      
        <div class="badge-container">
            @foreach ($badges as $badgeTag)
                @php
                    $badge = $badgeTag->pivot->badge;
                    $score = $badgeTag->pivot->score;
                    $icon = $badgeIcons[$badge] ?? '<div class="badge-icon default"></div>';

                    $badgeLabels = [
                        'bronz1' => 'برنز ۱',
                        'bronz2' => 'برنز ۲',
                        'bronz3' => 'برنز ۳',
                        'silver1' => 'نقره‌ای ۱',
                        'silver2' => 'نقره‌ای ۲',
                        'silver3' => 'نقره‌ای ۳',
                        'gold1' => 'طلایی ۱',
                        'gold2' => 'طلایی ۲',
                        'gold3' => 'طلایی ۳',
                        'platinum1' => 'پلاتینیوم ۱',
                        'platinum2' => 'پلاتینیوم ۲',
                        'platinum3' => 'پلاتینیوم ۳',
                        'dimond1' => 'الماس ۱',
                        'dimond2' => 'الماس ۲',
                        'dimond3' => 'الماس ۳',
                        'legendary1' => 'افسانه‌ای ۱',
                        'legendary2' => 'افسانه‌ای ۲',
                        'legendary3' => 'افسانه‌ای ۳',
                    ];
                    $label = $badgeLabels[$badge] ?? $badge;
                @endphp

                <div class="badge-card">
                    <div class="badge-icon-wrapper">
                        {!! $icon !!}
                    </div>
                    <div class="badge-label">{{ $label }}</div>
                    <div class="badge-score">({{ number_format($score) }} امتیاز)</div>
                    <div class="badge-tag">{{ $badgeTag->name }}</div>
                </div>
            @endforeach
        </div>

    @endif


</div>
@endsection







@section('scripts')
    <script>
        $(document).ready(function() {

        });
    </script>
@endsection