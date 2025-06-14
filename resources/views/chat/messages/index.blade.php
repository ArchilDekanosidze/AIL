@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{ asset('assets/css/chat/messages/index.css') }}">
{{-- Add specific CSS for reactions if you have it --}}

@endsection

@section('content')
<div class="CategoryBook main-body">
    <div class="mainDivDirection">
        <h2>Conversation: {{ $conversation->title ?? 'Private Chat' }}</h2>

        <div class="messages-box" id="messagesBox"
            data-conversation-id="{{ $conversation->id }}"
            data-user-id="{{ Auth::id() }}" {{-- Pass current user ID for client-side check --}}
            style="max-height: 400px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">
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
    </div>
</div>
@endsection

@section('scripts')
@vite('resources/js/app.js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> {{-- Ensure jQuery is loaded if not already by master --}}

<script>
    const currentUserId = {{ Auth::id() }}; // Get current user ID from Blade

    // Helper function to render a single message with reactions
    function renderMessage(msg, prepend = false) {
        // Ensure msg.reactions is an object if not present
        const reactions = msg.reactions || {};
        const currentUserReactionEmoji = msg.current_user_reaction || null; // From index endpoint

        let reactionsHtml = '';
        if (Object.keys(reactions).length > 0) {
            for (const emojiData of reactions) { // reactions is an array of objects from index endpoint
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

        const messageHtml = `
            <div class="message-item" data-id="${msg.id}">
                <div class="message-content">
                    <strong>${msg.sender?.name || 'Unknown'}:</strong>
                    <p>${msg.content}</p>
                    ${(msg.attachments || []).map(att =>
                        `<a href="/chat/attachments/${att.id}/download" target="_blank">
                            ${att.file_path.split('/').pop()}
                        </a><br>`
                    ).join('')}
                </div>
                <div class="message-meta">
                    <small>${msg.created_at}</small>
                    <button class="add-reaction-btn" data-message-id="${msg.id}">+</button>
                </div>
                <div class="reactions-summary" id="reactions-summary-${msg.id}">
                    ${reactionsHtml}
                </div>
            </div>
        `;

        if (prepend) {
            $('#messagesBox').prepend(messageHtml);
        } else {
            $('#messagesBox').append(messageHtml);
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

                data.reactions.forEach(reactionGroup => {
                    const isUserReactedWithThis = data.current_user_reaction === reactionGroup.emoji;
                    reactionsContainer.append(`
                        <span class="reaction-bubble ${isUserReactedWithThis ? 'user-reacted' : ''}"
                              data-message-id="${messageId}"
                              data-emoji="${reactionGroup.emoji}"
                              data-users='${JSON.stringify(reactionGroup.users)}'>
                            ${reactionGroup.emoji} ${reactionGroup.count}
                        </span>
                    `);
                });
            }
        });
    }

    // --- Document Ready for Message Loading & Sending ---
    $(document).ready(function () {
        const $messagesBox = $('#messagesBox');
        const conversationId = $messagesBox.data('conversation-id');
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
                         $messagesBox.append('<p style="text-align: center; color: #888;">No messages yet.</p>');
                    }
                    loading = false;
                    return;
                }

                const scrollPosition = $messagesBox[0].scrollHeight - $messagesBox.scrollTop();
                let initialScroll = !prepend && messages.length > 0; // Only scroll to bottom on first load

                // Clear "No messages yet" if present
                $messagesBox.find('p:contains("No messages yet.")').remove();

                messages.forEach(msg => {
                    renderMessage(msg, prepend); // Use the new render function
                    // After rendering, fetch and display reactions for each message
                    fetchAndRenderReactions(msg.id);
                });

                // Update earliestMessageId
                earliestMessageId = $messagesBox.find('.message-item').first().data('id');

                if (initialScroll) {
                    $messagesBox.scrollTop($messagesBox[0].scrollHeight);
                } else if (prepend) {
                    // Maintain scroll position on top load
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
                    // response will now contain a full message object
                    renderMessage(response, false); // Append new message
                    fetchAndRenderReactions(response.id); // Load reactions for the new message

                    form.reset();
                    $messagesBox.scrollTop($messagesBox[0].scrollHeight);
                },
                error: function (xhr) {
                    alert('Error sending message.');
                    console.error(xhr.responseText);
                }
            });
        });

        // --- Real-time Message Updates ---
        if (typeof window.Echo !== 'undefined') {
            window.Echo.private(`chat.conversation.${conversationId}`)
                .listen('MessageSent', (e) => {
                    renderMessage(e.message, false); // Use the new render function, assuming 'e.message' is the full message object
                    fetchAndRenderReactions(e.message.id); // Load reactions for the new real-time message
                    $messagesBox.scrollTop($messagesBox[0].scrollHeight);
                })
                // Add a listener for reaction updates
                .listen('MessageReactionUpdated', (e) => {
                    // e.messageId, e.userId, e.emoji, e.status
                    fetchAndRenderReactions(e.message_id); // Re-fetch all reactions for the message
                });
        } else {
            console.error('window.Echo is not defined. Real-time features might not work.');
        }
    });

    // --- Reaction Functionality (outside document.ready for organization) ---
    $(document).ready(function() {
        const $messagesBox = $('#messagesBox');
        const $emojiPickerTemplate = $('#emojiPickerTemplate');
        let activePicker = null; // To keep track of the currently open picker

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
            const $picker = $emojiPickerTemplate.clone().removeAttr('id'); // Removed .show() temporarily


            $picker.data('message-id', messageId); // Store message ID on the picker

            // Append it to the message item
            $messageItem.append($picker);
            // IMPORTANT: Use browser's DevTools "Elements" tab immediately here
            // AFTER you click the button, go to Elements tab and manually look inside the specific
            // <div class="message-item"> that you clicked. Do you see the <div class="emoji-picker-container"> inside it?

            activePicker = $picker; // Set the active picker AFTER appending

            // Now, try to show it after it's in the DOM
            $picker.show();

            // Optional: Adjust picker position
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

            // If the user clicks their own already applied reaction, it should toggle off.
            // If they click someone else's, or a different reaction, it might trigger the picker.
            // For simplicity, let's make clicking any bubble act like selecting that emoji.
            // The backend handles the add/remove/change logic.
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

        // Hide emoji picker when clicking anywhere else on the document
        $(document).on('click', function(e) {
            if (activePicker && !$(e.target).closest('.message-item').length && !$(e.target).is('.add-reaction-btn')) {
                activePicker.remove();
                activePicker = null;
            }
        });
    });
</script>
@endsection