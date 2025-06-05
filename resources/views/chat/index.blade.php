 @extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{ asset('assets/css/category/categoryBook/index.css') }}">
@endsection

@section('content')
<div class="CategoryBook main-body">    
    <div class="mainDivDirection">
        <h3 class="mb-3">لیست گفت‌وگوها</h3>
        <ul class="list-group">
            @forelse($conversations as $conversation)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <a href="{{ route('chat.conversations.show', $conversation->id) }}">
                        {{ $conversation->title ?? 'بدون عنوان' }}
                    </a>
                    <span class="badge bg-secondary">{{ $conversation->type }}</span>
                </li>
            @empty
                <li class="list-group-item">هیچ گفت‌وگویی یافت نشد.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        // Add chat index JS logic if needed
    });
</script>
@endsection
