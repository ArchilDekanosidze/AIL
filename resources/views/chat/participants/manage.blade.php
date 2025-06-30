@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{ asset('assets/css/chat/messages/index.css') }}">
<style>
    .user-result-item {
        cursor: pointer;
        padding: 0.25rem 0.5rem;
    }
    .user-result-item:hover {
        background-color: #f0f0f0;
    }
</style>
@endsection

@section('content')
<div class="CategoryBook main-body">
    <div class="mainDivDirection">

        {{-- Back to conversation --}}
        <a href="{{ route('chat.messages.index', $conversation->id) }}" class="btn btn-link mb-3">
            ← بازگشت به {{ $conversation->persianType }}
        </a>

        <h2 class="mb-4">مدیریت اعضا در: {{ $conversation->display_title ??  $conversation->persianType}}</h2>

        {{-- Search form --}}
        <form id="participantSearchForm" class="mb-3">
            <input
                type="text"
                id="participantSearchInput"
                name="search"
                placeholder="جست و حوی اعضا..."
                autocomplete="off"
                class="form-control"
                value="{{ request('search') ?? '' }}"
            />
        </form>


        <form method="GET" action="{{ route('chat.participants.manage', $conversation->id) }}" class="mb-4 d-flex gap-2 align-items-center">

            {{-- Filter Banned --}}
            <button type="submit" name="filter" value="banned" class="btn btn-danger ml-2" 
                @if(request('filter') === 'banned') disabled @endif>
                نمایش مسدود شده ها
            </button>

            {{-- Filter Muted --}}
            <button type="submit" name="filter" value="muted" class="btn btn-warning ml-2"
                @if(request('filter') === 'muted') disabled @endif>
                نمایش ساکت شده ها
            </button>

            {{-- Show All --}}
            @if(request()->has('filter'))
                <a href="{{ route('chat.participants.manage', $conversation->id) }}" class="btn btn-outline-primary ml-2">نمایش همه</a>
            @endif

        </form>


        {{-- Users table container --}}
        <div id="manageUsersTable">
            @include('partials.manage-users-search-results', [
                'participants' => $participants,
                'conversation' => $conversation,
                'canPromote' => $canPromote,
                'canDemote' => $canDemote
            ])
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function() {
    let debounceTimer;

    // AJAX search participants on input
    $('#participantSearchInput').on('input', function() {
        clearTimeout(debounceTimer);
        const query = $(this).val();

        debounceTimer = setTimeout(() => {
            $.ajax({
                url: '{{ route("chat.participants.search", $conversation->id) }}',
                method: 'GET',
                data: { q: query },
                success: function(responseHtml) {
                    $('#manageUsersTable').html(responseHtml);
                },
                error: function() {
                    alert('Failed to load search results.');
                }
            });
        }, 300);
    });

    // AJAX form submit for Promote/Demote, Ban/Unban, Mute/Unmute
    $(document).on('submit', '.action-form', function(e) {
        e.preventDefault();
        let form = $(this);
        $.ajax({
            url: form.attr('action'),
            method: form.attr('method'),
            data: form.serialize(),
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function() {
                // Refresh the user list after action
                $('#participantSearchInput').trigger('input');
            },
            error: function() {
                alert('Action failed. Please try again.');
            }
        });
    });
});
</script>
@endsection
