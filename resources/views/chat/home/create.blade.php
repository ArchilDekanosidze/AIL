@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{ asset('assets/css/chat/home/create.css') }}">
@endsection

@section('content')
<div class="CategoryBook main-body">    
    <div class="mainDivDirection">
        <div class="chat-create-box">
            <h2 class="text-xl font-semibold mb-4">شروع گفت و گوی جدید</h2>

            <input type="text" id="userSearchInput" class="search-input" placeholder="جست و جو ...">

            <div id="userResults"></div>

            <div class="action-buttons">
                <button class="btn-group" data-type="group">گروه جدید</button>
                <button class="btn-channel" data-type="channel">کانال جدید</button>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $('#userSearchInput').on('input', function () {
        const query = $(this).val().trim();

        if (query.length < 2) {
            $('#userResults').empty();
            return;
        }

        $.get('/chat/search-entities', { q: query }, function (results) {
            let html = '';
            results.forEach(item => {
                if (item.type === 'user') {
                    html += `
                        <div class="user-result"
                            data-user-id="${item.id}"
                            data-user-name="${item.name}"
                            data-user-avatar="${item.avatar}">
                            <img src="${item.avatar}" class="user-avatar" />
                            <span>${item.name}</span>
                            <span class="badge">کاربر</span>
                        </div>
                    `;
                    
                } else if (item.type === 'conversation') {
                    html += `
                        <div class="conversation-result"
                            data-conversation-id="${item.id}"
                            data-conversation-type="${item.conversation_type}">
                            <img src="${item.avatar}" class="user-avatar" />
                            <span>${item.name}</span>
                            <span class="badge">${item.conversation_type === 'group' ? 'گروه عمومی' : 'کانال عمومی'}</span>
                        </div>
                    `;
                }
            });

            $('#userResults').html(html);
        });

    });

    

   $(document).on('click', '.user-result', function () {
        const userId = $(this).data('user-id');
        $.ajax({
            url: '/chat/start-conversation',
            type: 'POST',
            data: {
                user_id: userId,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                window.location.href = response.redirect_url;
            },
            error: function (xhr) {
                alert('Something went wrong');
            }
        });
    });

    $(document).on('click', '.conversation-result', function () {
        const conversationId = $(this).data('conversation-id');
        window.location.href = `/chat/conversations/${conversationId}/messages`;
    });


    $('.action-buttons button').on('click', function () {
        const type = $(this).data('type');
        window.location.href = `/chat/groups/create-group?type=${type}`;
    });
</script>
@endsection
