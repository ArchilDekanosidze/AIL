@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{ asset('assets/css/category/categoryBook/index.css') }}">
@endsection

@section('content')
<div class="CategoryBook main-body">
    <div class="mainDivDirection">
        <h2>Conversation: {{ $conversation->title ?? 'Private Chat' }}</h2>

        <div class="messages-box" id="messagesBox"
            data-conversation-id="{{ $conversation->id }}"
            style="max-height: 400px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">
        </div>

        <form id="messageForm" action="{{ route('chat.messages.store', $conversation->id) }}" method="POST" enctype="multipart/form-data" style="margin-top: 20px;">
            @csrf
            <div>
                <textarea name="content" class="form-control" placeholder="Type your message..." required></textarea>
            </div>

            <div style="margin-top: 10px;">
                <input type="file" name="attachments[]" multiple>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Send</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
@vite('resources/js/app.js') {{-- This MUST come before using window.Echo --}}
<script>
    $(document).ready(function () {
        const box = document.getElementById('messagesBox');
        if (box) box.scrollTop = box.scrollHeight;

        $('#messageForm').on('submit', function (e) {
            e.preventDefault();

            const form = $(this)[0];
            const formData = new FormData(form);

            $.ajax({
                url: form.action,
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    // Append new message to box
                    $('#messagesBox').append(`
                        <div class="message-item" style="margin-bottom: 10px;">
                            <strong>${response.sender_name}</strong>
                            <p>${response.content}</p>
                            ${response.attachments.map(att => `
                                <a href="${att.download_url}" target="_blank">${att.filename}</a><br>
                            `).join('')}
                            <small>Just now</small>
                        </div>
                    `);

                    form.reset();
                    if (box) box.scrollTop = box.scrollHeight;
                },
                error: function (xhr) {
                    alert('Error sending message.');
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function () {
        
        const $box = $('#messagesBox');
        const conversationId = $box.data('conversation-id');
        let earliestMessageId = null;
        let loading = false;
        let noMore = false;

        function loadMessages(before = null, prepend = false) {
            if (loading || noMore) return;
            loading = true;

            $.get(`{{ url('/chat/conversations') }}/${conversationId}/getMessages`, { before }, function (messages) {
                if (messages.length === 0) {
                    noMore = true;
                    return;
                }
            

                const scrollPosition = $box[0].scrollHeight - $box.scrollTop();

                messages.forEach(msg => {
                    const html = `
                        <div class="message-item" data-id="${msg.id}" style="margin-bottom: 10px;">
                            <strong>${msg.sender?.name || 'Unknown'}:</strong>
                            <p>${msg.content}</p>
                            ${(msg.attachments || []).map(att =>
                                `<a href="/chat/attachments/${att.id}/download" target="_blank">
                                    ${att.file_path.split('/').pop()}
                                </a><br>`
                            ).join('')}
                            <small>${msg.created_at}</small>
                        </div>
                    `;

                    if (prepend) {
                        $box.prepend(html);
                    } else {
                        $box.append(html);
                    }
                });

                // Update earliestMessageId
                earliestMessageId = $box.find('.message-item').first().data('id');

                if (!prepend) {
                    // Scroll to bottom on first load
                    $box.scrollTop($box[0].scrollHeight);
                } else {
                    // Maintain scroll position on top load
                    $box.scrollTop($box[0].scrollHeight - scrollPosition);
                }

                loading = false;
            });
        }

        // Initial load
        loadMessages();

        // Detect scroll to top
        $box.on('scroll', function () {
            if ($box.scrollTop() <= 50 && !loading && earliestMessageId) {
                loadMessages(earliestMessageId, true);
            }
        });
    });


</script>

<script>
    $(document).ready(function () {
        const conversationId = $('#messagesBox').data('conversation-id');

        if (typeof window.Echo !== 'undefined') {
            window.Echo.private(`chat.conversation.${conversationId}`)
                .listen('MessageSent', (e) => {
                    $('#messagesBox').append(`
                        <div class="message-item" style="margin-bottom: 10px;">
                            <strong>${e.sender.name}</strong>
                            <p>${e.content}</p>
                            ${e.attachments.map(att => `
                                <a href="${att.download_url}" target="_blank">${att.file_path.split('/').pop()}</a><br>
                            `).join('')}
                            <small>Just now</small>
                        </div>
                    `);

                    const box = document.getElementById('messagesBox');
                    if (box) box.scrollTop = box.scrollHeight;
                });
        } else {
            console.error('window.Echo is not defined');
        }
    });
</script>


@endsection
