@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{ asset('assets/css/category/categoryBook/index.css') }}">
@endsection

@section('content')
<div class="CategoryBook main-body">
    <div class="mainDivDirection">
        <h2>Create New Conversation</h2>

        <form method="POST" action="{{ route('chat.conversations.store') }}">
            @csrf
            <div>
                <label for="title">Conversation Title:</label>
                <input type="text" name="title" id="title" required>
            </div>
            <button type="submit">Create</button>
        </form>

        <div style="margin-top: 10px;">
            <a href="{{ route('chat.conversations.index') }}">← Back to Conversations</a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Optional JS
    });
</script>
@endsection
