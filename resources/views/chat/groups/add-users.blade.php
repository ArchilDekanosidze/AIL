@extends('layouts.master')

@section('style')
{{-- Consider moving this CSS to a dedicated file like public/assets/css/chat/groups/add-users.css --}}
<link rel="stylesheet" href="{{ asset('assets/css/chat/groups/add-users.css') }}">
<style>
    /* Main container for the chat creation box */
    .chat-create-box {
        background-color: #fff;
        padding: 2.5rem; /* Increased padding */
        border-radius: 0.75rem; /* Rounded corners */
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); /* Soft shadow */
        max-width: 600px; /* Max width for better readability */
        margin: 2rem auto; /* Center the box */
    }

    /* Input field styling */
    .search-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #d1d5db; /* Light gray border */
        border-radius: 0.5rem;
        font-size: 1rem;
        color: #374151; /* Dark gray text */
        transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .search-input:focus {
        border-color: #4f46e5; /* Indigo on focus */
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2); /* Focus ring */
        outline: none;
    }

    /* Styling for search results container */
    #memberSearchResults {
        max-height: 250px; /* Limit height for scrollability */
        overflow-y: auto; /* Enable vertical scrolling */
        background-color: #fff;
        border: 1px solid #e5e7eb; /* Lighter border */
        border-top: none; /* No top border if it's connected to input */
        border-radius: 0 0 0.5rem 0.5rem; /* Rounded bottom corners */
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .user-result-item {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        cursor: pointer;
        border-bottom: 1px solid #f3f4f6; /* Very light border between items */
        transition: background-color 0.15s ease-in-out;
    }
    .user-result-item:hover {
        background-color: #f9fafb; /* Lighter background on hover */
    }
    .user-result-item:last-child {
        border-bottom: none; /* No border for the last item */
    }
    .user-avatar {
        width: 40px; /* Larger avatar */
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 0.75rem;
        border: 1px solid #e5e7eb; /* Subtle border for avatars */
    }
    .user-result-item span {
        font-weight: 500; /* Medium font weight for name */
        color: #374151;
    }

    /* Selected members display */
    .selected-users-container {
        margin-top: 1.5rem; /* More space from search */
        padding: 1rem;
        border: 1px dashed #d1d5db; /* Dashed border to signify selection area */
        border-radius: 0.5rem;
        background-color: #f9fafb; /* Light background */
        min-height: 80px; /* Minimum height */
    }
    .selected-user-tag {
        display: inline-flex;
        align-items: center;
        background-color: #6366f1; /* Indigo background */
        color: white;
        padding: 0.5rem 0.75rem;
        border-radius: 1.5rem; /* More rounded pill shape */
        margin: 0.4rem; /* Spacing between tags */
        font-size: 0.875rem; /* Smaller font size */
        font-weight: 500;
    }
    .selected-user-tag .remove-btn {
        background: none;
        border: none;
        color: white;
        margin-left: 0.5rem;
        cursor: pointer;
        font-weight: bold;
        font-size: 1rem; /* Larger 'x' */
        line-height: 1; /* Align vertically */
        padding: 0 0.2rem;
        transition: color 0.2s ease-in-out;
    }
    .selected-user-tag .remove-btn:hover {
        color: #ef4444; /* Red on hover */
    }

    /* Action buttons (submit) */
    .action-buttons {
        display: flex;
        justify-content: flex-end;
        margin-top: 1.5rem;
    }
    .btn-submit-users {
        background-color: #22c55e; /* Green button */
        color: white;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 0.5rem;
        cursor: pointer;
        font-size: 1rem;
        font-weight: 600; /* Semi-bold */
        transition: background-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .btn-submit-users:hover {
        background-color: #16a34a; /* Darker green on hover */
        box-shadow: 0 4px 6px -1px rgba(34, 197, 94, 0.3), 0 2px 4px -1px rgba(34, 197, 94, 0.1);
    }
</style>
@endsection

@section('content')
<div class="CategoryBook main-body">    
    <div class="mainDivDirection">
        <div class="chat-create-box">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">
                Add Members to "{{ $conversation->title }}" ({{ ucfirst($conversation->type) }})
            </h2>

            <div class="user-search-container mb-6">
                <label for="memberSearchInput" class="block text-sm font-medium text-gray-700 mb-2">Search for users to add:</label>
                <input type="text" id="memberSearchInput" class="search-input" placeholder="Start typing a name...">
                <div id="memberSearchResults" class="mt-2"></div>
            </div>

            <div class="selected-users-container">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Selected Members:</h3>
                <div id="selectedMembersDisplay" class="flex flex-wrap gap-2 min-h-[40px]">
                    {{-- The creator (current user) is automatically added in the backend.
                         You could display them here if you fetch existing participants,
                         but for now, this section shows newly added members. --}}
                    <p class="text-gray-600 text-sm italic" id="noMembersMessage">No new members selected yet.</p>
                </div>
            </div>

            <div class="action-buttons">
                {{-- Using a form for submission. The hidden input will be updated by JS --}}
                <form id="addUsersForm" action="{{ route('chat.groups.add-users.store', $conversation->id) }}" method="POST">
                    @csrf
                    {{-- This hidden input will hold the comma-separated string of user IDs --}}
                    <input type="hidden" name="user_ids" id="finalSelectedUserIds">
                    <button type="submit" class="btn-submit-users">Finish Adding Members</button>
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

            $.get('/chat/search-users', { q: query }, function (users) {
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
                alert('Please select at least one new member to add, or click "Finish" if you have no one else to add.');
                e.preventDefault(); // Stop the form from submitting
            }
        });
    });
</script>
@endsection