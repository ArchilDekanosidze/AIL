@extends('layouts.master')

@section('style')
{{-- Consider moving this CSS to a dedicated file like public/assets/css/chat/groups/add-users.css --}}
<link rel="stylesheet" href="{{ asset('assets/css/chat/groups/add-users.css') }}">
@endsection

@section('content')
<div class="CategoryBook main-body">    
    <div class="mainDivDirection">
        <div class="chat-create-box">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">
                افزودن افراد به  "{{ $conversation->title }}" ({{ $conversation->persianType }})
            </h2>

            <div class="user-search-container mb-6">
                <label for="memberSearchInput" class="block text-sm font-medium text-gray-700 mb-2">جست و جوی اعضا برای افزودن</label>
                <input type="text" id="memberSearchInput" class="search-input" placeholder="شروع کنید به نوشتن اسم...">
                <div id="memberSearchResults" class="mt-2"></div>
            </div>

            <div class="selected-users-container">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">اعضای انتخاب شده: </h3>
                <div id="selectedMembersDisplay" class="flex flex-wrap gap-2 min-h-[40px]">
                    {{-- The creator (current user) is automatically added in the backend.
                         You could display them here if you fetch existing participants,
                         but for now, this section shows newly added members. --}}
                    <p class="text-gray-600 text-sm italic" id="noMembersMessage">عضو جدیدی هنوز انتخاب نشده است</p>
                </div>
            </div>

            <div class="action-buttons">
                {{-- Using a form for submission. The hidden input will be updated by JS --}}
                <form id="addUsersForm" action="{{ route('chat.groups.add-users.store', $conversation->id) }}" method="POST">
                    @csrf
                    {{-- This hidden input will hold the comma-separated string of user IDs --}}
                    <input type="hidden" name="user_ids" id="finalSelectedUserIds">
                    <button type="submit" class="btn-submit-users">اتمام افزودن اعضا</button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Use a Map to store { userId: { id, name, avatar } } for easy lookup and display
        let selectedUsers = new Map();

        // Function to update the displayed selected members and the hidden form input
        function updateSelectedMembersDisplay() {
            let html = '';

            if (selectedUsers.size === 0) {
                $('#noMembersMessage').show(); // Show the "No members selected" message
            } else {
                $('#noMembersMessage').hide(); // Hide it if members are selected
                selectedUsers.forEach(user => {
                    html += `
                        <span class="selected-user-tag" data-selected-user-id="${user.id}">
                            ${user.name}
                            <button type="button" class="remove-btn" data-remove-id="${user.id}">&times;</button>
                        </span>
                    `;
                });
            }
            $('#selectedMembersDisplay').html(html);

            // Update the hidden input for form submission with comma-separated IDs
            $('#finalSelectedUserIds').val(Array.from(selectedUsers.keys()).join(','));
        }

        // Initial update when the page loads
        updateSelectedMembersDisplay();

        // --- User Search Functionality ---
        $('#memberSearchInput').on('input', function () {
            const query = $(this).val().trim();

            if (query.length < 2) {
                $('#memberSearchResults').empty(); // Clear results if query is too short
                return;
            }

            $.get('/chat/groups/search-users', { q: query }, function (users) {
                let html = '';
                users.forEach(user => {
                    // Only display users not already selected
                    if (!selectedUsers.has(user.id)) {
                        html += `
                            <div class="user-result-item"
                                 data-user-id="${user.id}"
                                 data-user-name="${user.name}"
                                 data-user-avatar="${user.avatar ?? '/images/Site/default-avatar.png'}">
                                <img src="${user.avatar ?? '/images/Site/default-avatar.png'}" class="user-avatar" alt="${user.name} avatar" />
                                <span>${user.name}</span>
                            </div>
                        `;
                    }
                });
                $('#memberSearchResults').html(html);
            });
        });

        // --- Add User to Selected List ---
        // Delegated event listener for dynamically loaded search results
        $(document).on('click', '.user-result-item', function () {
            const userId = $(this).data('user-id');
            const userName = $(this).data('user-name');
            const userAvatar = $(this).data('user-avatar');

            // Add the user object to the Map if not already present
            if (!selectedUsers.has(userId)) {
                selectedUsers.set(userId, { id: userId, name: userName, avatar: userAvatar });
                updateSelectedMembersDisplay(); // Refresh display
                $('#memberSearchInput').val(''); // Clear search input
                $('#memberSearchResults').empty(); // Clear search results
            }
        });

        // --- Remove User from Selected List ---
        // Delegated event listener for dynamically added user tags
        $(document).on('click', '.remove-btn', function (e) {
            e.stopPropagation(); // Prevent potential parent click events
            const userIdToRemove = $(this).data('remove-id');
            selectedUsers.delete(userIdToRemove); // Remove from the Map
            updateSelectedMembersDisplay(); // Refresh display
        });

        // --- Form Submission ---
        $('#addUsersForm').on('submit', function(e) {
            // Before submitting, ensure the hidden input is correctly populated.
            // This is handled by updateSelectedMembersDisplay, but good to ensure logic runs.
            $('#finalSelectedUserIds').val(Array.from(selectedUsers.keys()).join(','));

            // Optional: Prevent submission if no new members are selected
            // (The owner is already added server-side, so this applies to additional members)
            if (selectedUsers.size === 0) {
                // alert('Please select at least one new member to add, or click "Finish" if you have no one else to add.');
                // e.preventDefault(); // Stop the form from submitting
            }
        });
    });
</script>
@endsection