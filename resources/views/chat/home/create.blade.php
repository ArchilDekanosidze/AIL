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

        $.get('/chat/search-users', { q: query }, function (users) {
            let html = '';
            users.forEach(user => {
                html += `
                    <div class="user-result" data-user-id="${user.id}">
                        <img src="${user.avatar ?? '/images/Site/default-avatar.png'}" class="user-avatar" />
                        <span>   ${user.name}</span>
                    </div>
                `;
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

    $('.action-buttons button').on('click', function () {
        const type = $(this).data('type');
        window.location.href = `/chat/groups/create-group?type=${type}`;
    });
</script>
@endsection
