  @extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{ asset('assets/css/category/categoryBook/index.css') }}">
@endsection

@section('content')
<div class="CategoryBook main-body">    
    <div class="mainDivDirection">
        <h3 class="mb-3">گفت‌وگو: {{ $conversation->title ?? 'بدون عنوان' }}</h3>

        <div id="chat-box" class="border p-3 mb-3" style="height: 300px; overflow-y: scroll; background: #f9f9f9;">
            @foreach($conversation->messages as $message)
                <div class="mb-2">
                    <strong>{{ $message->user->name }}:</strong> 
                    {{ $message->content }}
                    <br>
                    <small class="text-muted">{{ $message->created_at->diffForHumans() }}</small>
                </div>
            @endforeach
        </div>

        <form id="send-message-form" action="{{ route('chat.messages.store', $conversation->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group mb-2">
                <textarea name="content" class="form-control" placeholder="پیام خود را بنویسید..." required></textarea>
            </div>

            <div class="form-group mb-2">
                <input type="file" name="attachments[]" multiple class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">ارسال</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        // Optional: auto-scroll to bottom
        const chatBox = document.getElementById('chat-box');
        chatBox.scrollTop = chatBox.scrollHeight;
    });
</script>
@endsection
