@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{ asset('assets/css/category/categoryBook/index.css') }}">
@endsection

@section('content')
<div class="CategoryBook main-body">    
    <div class="mainDivDirection">
        <h2>{{ $conversation->title ?? 'Conversation' }}</h2>

        <div>            
            @foreach ($conversation->messages as $message)
                <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 5px;">
                    <strong>{{ $message->sender->name }}:</strong> {{ $message->body }}
                    <div>
                        @foreach ($message->attachments as $attachment)
                            <a href="{{ route('chat.attachments.download', $attachment->id) }}">
                                Download {{ basename($attachment->file_path) }}
                            </a><br>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <form method="POST" action="{{ route('chat.messages.store', $conversation->id) }}" enctype="multipart/form-data">
            @csrf
            <textarea name="body" placeholder="Type a message..." rows="3"></textarea><br>
            <input type="file" name="attachments[]" multiple><br>
            <button type="submit">Send</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // JS for chat messages
    });
</script>
@endsection
