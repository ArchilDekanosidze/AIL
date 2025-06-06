@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{ asset('assets/css/category/categoryBook/index.css') }}">
@endsection

@section('content')
<div class="CategoryBook main-body">    
    <div class="mainDivDirection">
        <h2>Conversations</h2>
        <ul>
            @foreach ($conversations as $conversation)
                <li>
                    <a href="{{ route('chat.conversations.show', $conversation->id) }}">
                        {{ $conversation->title ?? 'Untitled Conversation' }}
                    </a>
                </li>
            @endforeach
        </ul>

        <form method="POST" action="{{ route('chat.conversations.store') }}">
            @csrf
            <input type="text" name="title" placeholder="New Conversation Title" required>
            <button type="submit">Create</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // JS for index if needed
    });
</script>
@endsection
