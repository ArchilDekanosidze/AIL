@extends('layouts.master')

@section('style')
{{-- Your CSS file for chat messages --}}
<link rel="stylesheet" href="{{ asset('assets/css/chat/messages/index.css') }}">
@endsection

@section('content')
<div class="CategoryBook main-body">
    <div class="mainDivDirection">
        @if ($conversation->type !== 'private' && in_array($currentUserRole, ['admin', 'super_admin']))
            <a href="{{ route('chat.participants.manage', $conversation->id) }}" class="btn btn-secondary">
                Manage Users
            </a>
        @endif
        {{-- Using $conversation->display_title as discussed previously --}}
        <h2>Conversation: {{ $conversation->display_title ?? 'Private Chat' }}</h2>

        <div class="messages-box" id="messagesBox"
            data-conversation-id="{{ $conversation->id }}"
            data-user-id="{{ Auth::id() }}"> {{-- Pass current user ID for client-side check --}}
            {{-- Messages will be loaded here by JavaScript --}}
        </div>

        {{-- Hidden emoji picker template --}}
        <div id="emojiPickerTemplate" class="emoji-picker-container" style="display: none;">
            <span class="emoji-option" data-emoji="👍">👍</span>
            <span class="emoji-option" data-emoji="❤️">❤️</span>
            <span class="emoji-option" data-emoji="😂">😂</span>
            <span class="emoji-option" data-emoji="🔥">🔥</span>
            <span class="emoji-option" data-emoji="😢">😢</span>
            <span class="emoji-option" data-emoji="💯">💯</span>
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

        @php
            $user = Auth::user();
            $participant = $user ? $conversation->participants->firstWhere('user_id', $user->id) : null;
        @endphp
        
        <div id="message-access-info">
            @guest
                <div class="alert alert-info text-center">
                    Please <a href="{{ route('auth.login') }}">log in</a> to send messages.
                </div>
            @else
                @if ($conversation->type !== 'private' && !$participant)
                    <div class="text-center">
                        <form method="POST" action="{{ route('chat.conversation.participants.join', $conversation->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">Join {{ ucfirst($conversation->type) }}</button>
                        </form>
                    </div>
                @elseif ($conversation->type === 'channel' && !in_array($participant->role, ['admin', 'superadmin']))
                    <div class="alert alert-warning text-center">
                        Only admins can send messages in this channel.
                    </div>
                @endif
            @endguest
        </div>

       

    </div>
</div>
@endsection


{{-- Delete Confirmation Modal --}}
<div id="deleteConfirmationModal" class="delete-modal-overlay" style="display: none;">
    <div class="delete-modal-content">
        <h3>Delete Message?</h3>
        <p>Choose how you want to delete this message:</p>
        <div class="delete-modal-buttons">
            <button class="btn btn-danger" id="deleteForEveryoneBtn" data-message-id="">Delete For Everyone</button>
            <button class="btn btn-secondary" id="deleteForMyselfBtn" data-message-id="">Delete For Myself</button>
            <button class="btn btn-light" id="cancelDeleteBtn">Cancel</button>
        </div>
    </div>
</div>

@section('scripts')
{{-- Ensure jQuery is loaded. If your layouts.master already loads it, you can remove this. --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>
    window.currentUserId = @json(Auth::id());    
    window.conversationType = @json($conversation->type);


    window.isParticipant = @json($participant !== null);
    window.userRole = @json($participant->role ?? null); // null, 'member', 'admin', 'superadmin'
</script>



<script>


    document.addEventListener('DOMContentLoaded', function () {
        const sendBox = document.getElementById('messageForm');
        const isGuest = !window.currentUserId;
        const isParticipant = window.isParticipant;
        const conversationType = window.conversationType;
        const role = window.userRole;

        if (isGuest) {
            sendBox.style.display = 'none';
        } else if (!isParticipant && conversationType !== 'private') {
            sendBox.style.display = 'none'; // Show join form instead
        } else if (conversationType === 'channel' && !['admin', 'superadmin'].includes(role)) {
            sendBox.style.display = 'none'; // Not allowed to send in channel
        } else {
            sendBox.style.display = 'block'; // Can send messages
        }
    });

    // Ensure currentUserId and conversationId are globally accessible in this script scope
    const currentUserId = @json(Auth::id());
    const conversationId = $('#messagesBox').data('conversation-id');



    // Ensure currentUserId and conversationId are globally accessible in this script scope
    // (These lines should be outside your renderMessage function, at the top of your script block)
    // const currentUserId = {{ Auth::id() }};
    // const conversationId = $('#messagesBox').data('conversation-id');

    // Helper function to render a single message with reactions
    function renderMessage(msg, prepend = false) {
        // This check ensures we don't try to render messages that are (globally) deleted
        // or deleted for the current user during initial load or `MessageSent` event.
        // `MessageDeleted` event listener will handle existing messages.
        // If the message is globally soft-deleted OR deleted for the current user,
        // and it's already in the DOM, replace it with a placeholder.
        if (msg.deleted_at || (msg.deleted_for_user_ids && msg.deleted_for_user_ids.includes(currentUserId))) {
            const $existingMessage = $(`.message-item[data-id="${msg.id}"]`);
            if ($existingMessage.length) {
                renderDeletedMessagePlaceholder(msg.id); // Use the placeholder logic
            }
            return; // Do not render if deleted for everyone or for current user
        }

        // IMPORTANT: Ensure 'reactions' is an array, as your backend sends it as such.
        const reactions = msg.reactions || [];
        // Assuming msg.current_user_reaction is directly the emoji string or null
        const currentUserReactionEmoji = msg.current_user_reaction || null;

        let reactionsHtml = '';
        if (reactions.length > 0) {
            // Sort reactions by count (most popular first) for consistency
            reactions.sort((a, b) => b.count - a.count);
            for (const emojiData of reactions) {
                const isUserReactedWithThis = currentUserReactionEmoji === emojiData.emoji;
                reactionsHtml += `
                    <span class="reaction-bubble ${isUserReactedWithThis ? 'user-reacted' : ''}"
                        data-message-id="${msg.id}"
                        data-emoji="${emojiData.emoji}"
                        data-users='${JSON.stringify(emojiData.users)}'>
                        ${emojiData.emoji} ${emojiData.count}
                    </span>
                `;
            }
        }

        // Determine if it's an edited message and get the formatted time
        const editedDisplay = msg.edited_at ? `<small class="edited-timestamp">(Edited: ${msg.edited_at})</small>` : '';

        // Determine if the current user is the sender to show edit/delete buttons
        const isSender = (currentUserId === msg.sender?.id);

        // --- START OF ATTACHMENT RENDERING LOGIC ---
        const attachmentsHtml = (msg.attachments || []).map(att => {
            // `att.file_path` should now be a publicly accessible URL (e.g., /storage/...)
            const downloadUrl = `/chat/attachments/${att.id}/download`; // Your dedicated download route

            const mimeType = att.mime_type || ''; // Ensure mime_type is available
            const fileName = att.file_name || 'Download File'; // Ensure file_name is available
            let attachmentDisplay = '';

            if (mimeType.startsWith('image/')) {
                // It's an image
                attachmentDisplay = `
                    <div class="message-attachment-item image-attachment">
                        <a href="${downloadUrl}" target="_blank" title="Download ${fileName}">
                            <img src="${att.file_path}" alt="${fileName}" loading="lazy" class="attachment-image">
                        </a>
                        <div class="attachment-filename">${fileName}</div>
                        <a href="${downloadUrl}" target="_blank" class="download-link">Download</a>
                    </div>
                `;
            } else if (mimeType.startsWith('video/')) {
                // It's a video
                // If you have a video thumbnail (poster) URL from backend, use att.thumbnail_url || ''
                attachmentDisplay = `
                    <div class="message-attachment-item video-attachment">
                        <video controls preload="none" class="attachment-video">
                            <source src="${att.file_path}" type="${mimeType}">
                            Your browser does not support the video tag.
                        </video>
                        <div class="attachment-filename">${fileName}</div>
                        <a href="${downloadUrl}" target="_blank" class="download-link">Download</a>
                    </div>
                `;
            }else if (mimeType.startsWith('audio/')) { // <--- NEW: Handle Audio Files
                // It's an audio file
                attachmentDisplay = `
                    <div class="message-attachment-item audio-attachment">
                        <audio controls preload="none" class="attachment-audio">
                            <source src="${att.file_path}" type="${mimeType}">
                            Your browser does not support the audio tag.
                        </audio>
                        <div class="attachment-filename">${fileName}</div>
                        <a href="${downloadUrl}" target="_blank" class="download-link">Download</a>
                    </div>
                `;
            } 
            else {
                // It's another type of file (PDF, Doc, etc.)
                attachmentDisplay = `
                    <div class="message-attachment-item file-attachment">
                        <a href="${downloadUrl}" target="_blank" class="message-attachment-link">
                            ${fileName}
                        </a>
                        <a href="${downloadUrl}" target="_blank" class="download-link">Download</a>
                    </div>
                `;
            }
            return attachmentDisplay;
        }).join(''); // Join all generated HTML snippets
        // --- END OF ATTACHMENT RENDERING LOGIC ---

        const messageHtml = `
            <div class="message-item" data-id="${msg.id}">
                <div class="message-content">
                    <strong>${msg.sender?.name || 'Unknown'}:</strong>
                    <p class="message-text">${msg.content}</p>
                    ${attachmentsHtml} </div>
                <div class="message-meta">
                    <small>${msg.created_at}</small> ${editedDisplay}
                    ${isSender ? `<button class="btn btn-sm btn-info edit-message-btn" data-id="${msg.id}" data-content="${encodeURIComponent(msg.content)}">Edit</button>` : ''}
                    ${isSender ? `<button class="btn btn-sm btn-danger delete-message-btn" data-id="${msg.id}">Delete</button>` : ''}
                    <button class="add-reaction-btn" data-message-id="${msg.id}">+</button>
                </div>
                <div class="reactions-summary" id="reactions-summary-${msg.id}">
                    ${reactionsHtml}
                </div>
            </div>
        `;

        // --- CRITICAL CHANGE BLOCK START (for replacing/appending messages) ---
        const $existingMessage = $(`.message-item[data-id="${msg.id}"]`);
        const $messagesBox = $('#messagesBox'); // Ensure this is accessible or passed
        const $newMessage = $(messageHtml); // <-- wrap string in jQuery object

        if ($existingMessage.length) {
            // If the message already exists, replace its entire HTML content.
            // This handles edits and initial loads where a message might be marked as deleted.
            $existingMessage.replaceWith($newMessage);
        } else {
            // If it's a new message, append or prepend based on the flag.
            if (prepend) {
                $messagesBox.prepend($newMessage);
            } else {
                $messagesBox.append($newMessage);
            }
        }

       disableReactionButtonsIfNeeded($newMessage[0]); // Pass DOM element
        // --- CRITICAL CHANGE BLOCK END ---
    }

    function disableReactionButtonsIfNeeded(messageElement) {
        if (!messageElement) return;

        const isGuest = !window.currentUserId;
        const isNotParticipant = !window.isParticipant;

        if (isGuest || isNotParticipant) {
            messageElement.querySelectorAll('.add-reaction-btn').forEach(button => {
                button.disabled = true;
                button.classList.add('opacity-50', 'cursor-not-allowed');
                button.title = "Login and join to react";
            });
        }
    }






    // Function to replace a message with a deleted placeholder
    function renderDeletedMessagePlaceholder(messageId) {
        const $existingMessage = $(`.message-item[data-id="${messageId}"]`);
        if ($existingMessage.length) {
            $existingMessage.replaceWith(`
                <div class="message-item deleted-message-placeholder" data-id="${messageId}">
                    <p><em>This message was deleted.</em></p>
                </div>
            `);
        }
    }

    // Function to fetch and display reactions for a specific message
    function fetchAndRenderReactions(messageId) {
        $.get(`{{ url('/chat/messages') }}/${messageId}/reactions`, function(data) {
            const reactionsContainer = $(`#reactions-summary-${messageId}`);
            reactionsContainer.empty(); // Clear existing reactions

            if (data.reactions && data.reactions.length > 0) {
                // Sort reactions by count (most popular first) for consistency
                data.reactions.sort((a, b) => b.count - a.count);

                data.reactions.forEach(reactionGroup => { // Correct loop variable
                    const isUserReactedWithThis = data.current_user_reaction === reactionGroup.emoji;
                    reactionsContainer.append(`
                        <span class="reaction-bubble ${isUserReactedWithThis ? 'user-reacted' : ''}"
                            data-message-id="${messageId}"
                            data-emoji="${reactionGroup.emoji}"
                            data-users='${JSON.stringify(reactionGroup.users)}'>
                            ${reactionGroup.emoji} ${reactionGroup.count} {{-- CORRECTED: Use reactionGroup --}}
                        </span>
                    `);
                });
            }
        });
    }

    // --- Document Ready for Message Loading & Sending ---
    $(document).ready(function () {
        const $messagesBox = $('#messagesBox');
        let earliestMessageId = null;
        let loading = false;
        let noMore = false;

        function loadMessages(before = null, prepend = false) {
            if (loading || noMore) return;
            loading = true;

            $.get(`{{ url('/chat/conversations') }}/${conversationId}/getMessages`, { before }, function (messages) {
                if (messages.length === 0) {
                    noMore = true;
                    if ($messagesBox.children().length === 0) {
                         $messagesBox.append('<p class="no-messages-yet">No messages yet.</p>');
                    }
                    loading = false;
                    return;
                }

                const scrollPosition = $messagesBox[0].scrollHeight - $messagesBox.scrollTop();
                let initialScroll = !prepend && messages.length > 0;

                $messagesBox.find('.no-messages-yet').remove(); // Clear "No messages yet" if present

                messages.forEach(msg => {
                    renderMessage(msg, prepend);
                    fetchAndRenderReactions(msg.id);
                });

                earliestMessageId = $messagesBox.find('.message-item').first().data('id');

                if (initialScroll) {
                    $messagesBox.scrollTop($messagesBox[0].scrollHeight);
                } else if (prepend) {
                    $messagesBox.scrollTop($messagesBox[0].scrollHeight - scrollPosition);
                }

                loading = false;
            }).fail(function() {
                loading = false;
                console.error("Failed to load messages.");
            });
        }

        // Initial load
        loadMessages();

        // Detect scroll to top for loading more messages
        $messagesBox.on('scroll', function () {
            if ($messagesBox.scrollTop() <= 50 && !loading && earliestMessageId) {
                loadMessages(earliestMessageId, true);
            }
        });

        // --- Message Sending ---
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
                    renderMessage(response, false); // Append new message
                    fetchAndRenderReactions(response.id);
                    form.reset();
                    $messagesBox.scrollTop($messagesBox[0].scrollHeight);
                },
                error: function (xhr) {
                    alert('Error sending message.');
                    console.error(xhr.responseText);
                }
            });
        });

        // --- Message Editing ---
        let editingMessageId = null;
        let originalMessageContent = '';

        $messagesBox.on('click', '.edit-message-btn', function() {
            const messageId = $(this).data('id');
            const messageContent = decodeURIComponent($(this).data('content'));

            if (editingMessageId && editingMessageId !== messageId) {
                alert('Please finish editing the current message first.');
                return;
            }

            editingMessageId = messageId;
            originalMessageContent = messageContent;

            const $messageContentP = $(this).closest('.message-item').find('.message-content .message-text');
            $messageContentP.html(`
                <textarea class="form-control editing-textarea" rows="3">${messageContent}</textarea>
                <div class="edit-buttons">
                    <button class="btn btn-sm btn-success save-edit-btn">Save</button>
                    <button class="btn btn-sm btn-secondary cancel-edit-btn">Cancel</button>
                </div>
            `);
        });

        // Save Edit
// Save Edit
        $messagesBox.on('click', '.save-edit-btn', function() {
            const $messageItem = $(this).closest('.message-item');
            const messageId = $messageItem.data('id');
            const newContent = $messageItem.find('.editing-textarea').val();

            if (!newContent.trim()) {
                alert('Message content cannot be empty.');
                return;
            }

            $.ajax({
                url: `/chat/messages/${messageId}`,
                method: 'PUT', // Use PUT method for update
                data: {
                    content: newContent,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log('Message updated:', response);
                    // Reset editing state. This is important.
                    editingMessageId = null;

                    // Call renderMessage. It will find the existing message and replace it,
                    // automatically removing the editing textarea and buttons.
                    renderMessage(response.updated_message, false);

                    // No need to fetchAndRenderReactions explicitly here if your
                    // `response.updated_message` from the backend already contains
                    // the updated reactions for `renderMessage` to process.
                },
                error: function(xhr) {
                    alert('Error updating message.');
                    console.error(xhr.responseText);
                    // On error, revert the message content and remove the edit box manually
                    const $messageContentP = $messageItem.find('.message-content .message-text');
                    $messageContentP.text(originalMessageContent); // Revert to original text
                    editingMessageId = null; // Reset editing state
                    // Also remove the buttons and textarea
                    $messageContentP.siblings('.edit-buttons').remove(); // Remove the buttons
                    $messageContentP.find('.editing-textarea').remove(); // Remove the textarea if still there
                }
            });
        });

        // Cancel Edit
        $messagesBox.on('click', '.cancel-edit-btn', function() {
            const $messageItem = $(this).closest('.message-item');
            const $messageContentP = $messageItem.find('.message-content .message-text');

            // Revert content
            $messageContentP.text(originalMessageContent); // Revert

            // --- ADD THESE LINES ---
            $messageContentP.siblings('.edit-buttons').remove(); // Remove the buttons div
            $messageContentP.find('.editing-textarea').remove(); // Remove the textarea
            // --- END ADDITIONS ---

            editingMessageId = null; // Reset
        });

        // --- Message Deletion ---
        // --- Message Deletion (using custom modal) ---
        const $deleteConfirmationModal = $('#deleteConfirmationModal');
        const $deleteForEveryoneBtn = $('#deleteForEveryoneBtn');
        const $deleteForMyselfBtn = $('#deleteForMyselfBtn');
        const $cancelDeleteBtn = $('#cancelDeleteBtn');

        // Show delete confirmation modal when delete button is clicked
        $messagesBox.on('click', '.delete-message-btn', function() {
            const messageId = $(this).data('id');
            // Set the message ID on the modal buttons so we know which message to delete
            $deleteForEveryoneBtn.data('message-id', messageId);
            $deleteForMyselfBtn.data('message-id', messageId);
            $deleteConfirmationModal.fadeIn(); // Show the modal
        });

        // Handle "Delete For Everyone" click
        $deleteForEveryoneBtn.on('click', function() {
            const messageId = $(this).data('message-id');
            $.ajax({
                url: `/chat/messages/${messageId}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}',
                    deletion_type: 'for_everyone'
                },
                success: function(response) {
                    console.log('Message deletion (for everyone) request sent:', response);
                    $deleteConfirmationModal.fadeOut(); // Hide modal
                    // UI update handled by WebSocket listener
                    // ADD THIS LINE TO UPDATE CURRENT PAGE:
                    renderDeletedMessagePlaceholder(messageId);
                },
                error: function(xhr) {
                    alert('Error deleting message for everyone.');
                    console.error(xhr.responseText);
                    $deleteConfirmationModal.fadeOut(); // Hide modal on error
                }
            });
        });

        // Handle "Delete For Myself" click
        $deleteForMyselfBtn.on('click', function() {
            const messageId = $(this).data('message-id');
            $.ajax({
                url: `/chat/messages/${messageId}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}',
                    deletion_type: 'for_me'
                },
                success: function(response) {
                    console.log('Message deletion (for myself) request sent:', response);
                    $deleteConfirmationModal.fadeOut(); // Hide modal
                    // UI update handled by WebSocket listener (specifically for current user)

                    // ADD THIS LINE TO UPDATE CURRENT PAGE:
                    $(`.message-item[data-id="${messageId}"]`).remove();
                },
                error: function(xhr) {
                    alert('Error deleting message for yourself.');
                    console.error(xhr.responseText);
                    $deleteConfirmationModal.fadeOut(); // Hide modal on error
                }
            });
        });

        // Handle "Cancel" click
        $cancelDeleteBtn.on('click', function() {
            $deleteConfirmationModal.fadeOut(); // Just hide the modal
        });

        // --- Real-time Message Updates (Echo Listeners) ---
        if (typeof window.Echo !== 'undefined') {
            const channelType = @json($conversation->type === 'private' ? 'private' : 'public');
            const chatChannel = channelType === 'private'
                ? window.Echo.private(`chat.conversation.${conversationId}`)
                : window.Echo.channel(`chat.conversation.${conversationId}`);

            chatChannel
                .listen('.MessageSent', (e) => { // 'e' contains the message object
                    console.log('MessageSent event received:', e);
                    renderMessage(e.message, false);
                    fetchAndRenderReactions(e.message.id);
                    $messagesBox.scrollTop($messagesBox[0].scrollHeight);
                })
                .listen('.MessageReactionUpdated', (e) => {
                    console.log('Reaction updated event received:', e);
                    fetchAndRenderReactions(e.message_id);
                })
                .listen('.MessageEdited', (e) => { // Listener for edited messages
                    console.log('MessageEdited event received:', e);
                    const $existingMessage = $(`.message-item[data-id="${e.message.id}"]`);
                    if ($existingMessage.length) {
                        // Remove old message (to re-render with new content/status)
                        $existingMessage.remove();
                        // Render the updated message
                        renderMessage(e.message, false); // Consider if you need to maintain order here
                        $messagesBox.scrollTop($messagesBox[0].scrollHeight); // Keep scroll at bottom if new
                    }
                })
                .listen('.MessageDeleted', (e) => {
                    console.log('MessageDeleted (conversation channel) event received:', e);
                    const $messageItem = $(`.message-item[data-id="${e.messageId}"]`);

                    if ($messageItem.length) {
                        if (e.deletionType === 'for_everyone') {
                            // If deleted for everyone, replace with a placeholder
                            renderDeletedMessagePlaceholder(e.messageId);
                        }
                        // IMPORTANT: The 'for_me' logic is removed from here
                        // because those events no longer come through this channel.
                    }
                });
                // --- END MODIFIED LISTENER ---
                            // 2. NEW LISTENER FOR USER-SPECIFIC EVENTS (specifically "delete for myself")
            // This channel 'user.{userId}' is where 'for_me' deletions are broadcast.
            window.Echo.private(`user.${currentUserId}`)
                .listen('.MessageDeleted', (e) => {
                    console.log('MessageDeleted (user channel) event received:', e);
                    const $messageItem = $(`.message-item[data-id="${e.messageId}"]`);

                    if ($messageItem.length) {
                        if (e.deletionType === 'for_me' && e.userId === currentUserId) {
                            // This specific user is the one for whom the message was deleted.
                            // Remove it from their view entirely.
                            $messageItem.remove();
                        }
                        // Other deletion types (like 'for_everyone') won't be broadcast here
                        // by your backend logic, but if they were, you'd handle them.
                    }
                });

        } else {
            console.error('window.Echo is not defined. Real-time features might not work.');
        }

    });

    // --- Reaction Functionality (outside document.ready for organization) ---
    $(document).ready(function() {
        const $messagesBox = $('#messagesBox');
        const $emojiPickerTemplate = $('#emojiPickerTemplate');
        let activePicker = null;

        // Show emoji picker when "+" button is clicked
        $messagesBox.on('click', '.add-reaction-btn', function() {
            const messageId = $(this).data('message-id');
            const $messageItem = $(this).closest('.message-item');

            // Hide any currently active picker
            if (activePicker) {
                activePicker.remove();
                activePicker = null;
            }

            // Clone the template and append to the message item
            const $picker = $emojiPickerTemplate.clone().removeAttr('id');

            $picker.data('message-id', messageId); // Store message ID on the picker

            // Append it to the message item
            $messageItem.append($picker);

            activePicker = $picker; // Set the active picker AFTER appending

            // Now, try to show it after it's in the DOM
            $picker.show();

            // Optional: Adjust picker position to be above the message if too low
            const pickerTop = $picker.offset().top - $messagesBox.offset().top;
            if (pickerTop < 0) {
                $picker.css({
                    'top': 'auto',
                    'bottom': 'unset',
                    'margin-top': '5px'
                });
            }
        });

        // Handle emoji selection
        $messagesBox.on('click', '.emoji-option', function() {
            const emoji = $(this).data('emoji');
            const messageId = $(this).closest('.emoji-picker-container').data('message-id');

            $.ajax({
                url: `/chat/messages/${messageId}/reactions`,
                method: 'POST',
                data: {
                    emoji: emoji,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log('Reaction updated:', response);
                    // Instead of simple update, re-fetch for comprehensive reaction display
                    fetchAndRenderReactions(messageId);
                    // Close the picker
                    if (activePicker) {
                        activePicker.remove();
                        activePicker = null;
                    }
                },
                error: function(xhr) {
                    alert('Error reacting to message.');
                    console.error(xhr.responseText);
                }
            });
        });

        // Handle clicking on an existing reaction bubble (to remove/change)
        $messagesBox.on('click', '.reaction-bubble', function() {
            const messageId = $(this).data('message-id');
            const emoji = $(this).data('emoji'); // The emoji of the bubble clicked

             $.ajax({
                url: `/chat/messages/${messageId}/reactions`,
                method: 'POST',
                data: {
                    emoji: emoji,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log('Reaction updated via bubble click:', response);
                    fetchAndRenderReactions(messageId);
                    // Close the picker if open
                    if (activePicker) {
                        activePicker.remove();
                        activePicker = null;
                    }
                },
                error: function(xhr) {
                    alert('Error reacting to message.');
                    console.error(xhr.responseText);
                }
            });
        });

        // Hide emoji picker when clicking anywhere else on the document (excluding the add button)
        $(document).on('click', function(e) {
            // Check if the click was outside the active picker AND not on a message item AND not on the add reaction button
            if (activePicker && !$(e.target).closest('.emoji-picker-container').length && !$(e.target).is('.add-reaction-btn')) {
                activePicker.remove();
                activePicker = null;
            }
        });
    });
</script>
@endsection