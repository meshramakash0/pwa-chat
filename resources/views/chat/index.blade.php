<x-app-layout>
    <style>
        /* WhatsApp color scheme */
        :root {
            --wa-teal-dark: #075e54;
            --wa-teal-light: #128c7e;
            --wa-green: #25d366;
            --wa-light-green: #dcf8c6;
            --wa-chat-bg: #e5ddd5;
            --wa-blue-check: #34b7f1;
        }

        /* Chat background pattern */
        .chat-background {
            background-color: var(--wa-chat-bg);
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23c9c5be' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        /* Message bubbles */
        .message-bubble {
            max-width: 75%;
            padding: 6px 12px 8px 12px;
            border-radius: 8px;
            position: relative;
            word-wrap: break-word;
            box-shadow: 0 1px 0.5px rgba(0,0,0,0.13);
            margin-top: 5px;
        }

        .message-sent {
            background-color: var(--wa-light-green);
            border-top-right-radius: 0;
            margin-left: auto;
        }

        .message-sent::after {
            content: '';
            position: absolute;
            right: -8px;
            top: 0;
            border: 8px solid transparent;
            border-left-color: var(--wa-light-green);
            border-top-color: var(--wa-light-green);
            border-top-right-radius: 3px;
        }

        .message-received {
            background-color: #ffffff;
            border-top-left-radius: 0;
        }

        .message-received::after {
            content: '';
            position: absolute;
            left: -8px;
            top: 0;
            border: 8px solid transparent;
            border-right-color: #ffffff;
            border-top-color: #ffffff;
            border-top-left-radius: 3px;
        }

        .message-time {
            font-size: 11px;
            color: rgba(0,0,0,0.45);
            margin-left: 8px;
            float: right;
            margin-top: 4px;
        }

        /* Scrollbar styling */
        .chat-messages::-webkit-scrollbar {
            width: 6px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: transparent;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background-color: rgba(0,0,0,0.2);
            border-radius: 3px;
        }

        /* Input focus */
        .chat-input:focus {
            outline: none;
        }

        /* Delete button styling */
        .delete-btn {
            opacity: 0.7;
            flex-shrink: 0;
        }
        
        .delete-btn:hover {
            opacity: 1;
        }

        /* Dropdown menu styles */
        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            min-width: 180px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            z-index: 50;
            overflow: hidden;
            margin-top: 8px;
        }

        .dropdown-menu.show {
            display: block;
            animation: dropdownFadeIn 0.15s ease-out;
        }

        @keyframes dropdownFadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            color: #333;
            text-decoration: none;
            transition: background-color 0.15s;
        }

        .dropdown-item:hover {
            background-color: #f5f5f5;
        }

        .dropdown-item svg {
            margin-right: 12px;
        }

        .dropdown-divider {
            height: 1px;
            background: #e5e5e5;
            margin: 4px 0;
        }

        /* Fixed layout */
        .chat-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
        }

        .chat-input-area {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 100;
        }

        .chat-messages-wrapper {
            flex: 1;
            overflow-y: auto;
            padding-top: 60px;
            padding-bottom: 70px;
        }

        @media (min-width: 640px) {
            .chat-messages-wrapper {
                padding-top: 68px;
                padding-bottom: 76px;
            }
        }
    </style>

    <div class="chat-container bg-gray-100">
        
        <!-- Chat Header (Fixed) -->
        <div class="chat-header flex items-center px-4 py-2 sm:py-3 text-white" style="background-color: var(--wa-teal-dark);">
            <!-- Back button (mobile) -->
            <a href="{{ route('chat.users') }}" class="mr-2 sm:mr-4 p-1 hover:bg-white/10 rounded-full transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            
            <!-- User Avatar -->
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-gray-300 flex items-center justify-center overflow-hidden flex-shrink-0">
                <svg class="w-6 h-6 sm:w-7 sm:h-7 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
            </div>
            
            <!-- User Info -->
            <div class="ml-3 flex-1 min-w-0">
                <h2 class="font-semibold text-base sm:text-lg truncate">{{ $user->name }}</h2>
                <p class="text-xs sm:text-sm text-green-200 truncate">online</p>
            </div>
            
            <!-- Action buttons -->
            <div class="flex items-center">
                <!-- 3 Dots Menu with Dropdown -->
                <div class="relative">
                    <button id="menuBtn" class="p-2 hover:bg-white/10 rounded-full transition">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                        </svg>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div id="dropdownMenu" class="dropdown-menu">
                        @if(auth()->id() == 1)
                            <button type="button" onclick="deleteAllMessages()" class="dropdown-item w-full text-left text-red-600">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete All Messages
                            </button>
                            <div class="dropdown-divider"></div>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="dropdown-item w-full text-left text-red-600">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Messages Area (Scrollable) -->
        <div class="chat-messages chat-messages-wrapper chat-background px-3 sm:px-6 py-4" id="chat-messages">
            @foreach($messages as $msg)
                @php
                    $isSent = $msg->sender_id === auth()->id();
                @endphp
                <div class="flex {{ $isSent ? 'justify-end' : 'justify-start' }} mb-3 message-container group" data-message-id="{{ $msg->id }}">
                    @if(auth()->id() == 1 && !$isSent)
                        {{-- Delete button on LEFT for received messages --}}
                        <button class="delete-btn self-center mr-1 p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-full transition-all" onclick="deleteMessage({{ $msg->id }})" title="Delete message">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    @endif
                    <div class="message-bubble {{ $isSent ? 'message-sent' : 'message-received' }}">
                        <span class="text-sm sm:text-base text-gray-800 message-text">{{ $msg->message }}</span>
                        <span class="message-time">
                            {{ $msg->formatted_time }}
                            @if($isSent)
                                {{-- Double tick: Blue if read, Gray if not --}}
                                <svg class="inline w-4 h-4 ml-1 tick-icon {{ $msg->read ? 'text-blue-500' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18 7l-1.41-1.41-6.34 6.34 1.41 1.41L18 7zm4.24-1.41L11.66 16.17 7.48 12l-1.41 1.41L11.66 19l12-12-1.42-1.41zM.41 13.41L6 19l1.41-1.41L1.83 12 .41 13.41z"/>
                                </svg>
                            @endif
                        </span>
                    </div>
                    @if(auth()->id() == 1 && $isSent)
                        {{-- Delete button on RIGHT for sent messages --}}
                        <button class="delete-btn self-center ml-1 p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-full transition-all" onclick="deleteMessage({{ $msg->id }})" title="Delete message">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Chat Input Area (Fixed) -->
        <div class="chat-input-area px-2 sm:px-4 py-2 sm:py-3 bg-gray-100 border-t border-gray-200">
            <form id="chat-form" class="flex items-center space-x-2">
                @csrf
                <input type="hidden" name="receiver_id" value="{{ $user->id }}">
                
                <!-- Emoji Button -->
                <button type="button" class="p-2 text-gray-500 hover:text-gray-700 transition hidden sm:block">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </button>
                
                <!-- Attachment Button -->
                <button type="button" class="p-2 text-gray-500 hover:text-gray-700 transition hidden sm:block">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                    </svg>
                </button>
                
                <!-- Message Input -->
                <div class="flex-1">
                    <input 
                        type="text" 
                        name="message" 
                        class="chat-input w-full px-4 py-2.5 sm:py-3 bg-white rounded-full border-0 text-sm sm:text-base shadow-sm focus:ring-2 focus:ring-green-500" 
                        placeholder="Type a message"
                        required
                        autocomplete="off"
                    >
                </div>
                
                <!-- Send Button -->
                <button 
                    type="submit" 
                    class="p-2.5 sm:p-3 rounded-full text-white transition-all duration-200 hover:scale-105 active:scale-95 flex-shrink-0"
                    style="background-color: var(--wa-teal-light);"
                >
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <script>
        // Wait for DOM to be ready
        document.addEventListener('DOMContentLoaded', function() {
            // Current user and chat partner IDs
            const currentUserId = {{ auth()->id() }};
            const chatPartnerId = {{ $user->id }};

            // Track the last message ID to avoid duplicates
            let lastMessageId = {{ $messages->last()?->id ?? 0 }};
            
            // Track message IDs we've already displayed
            const displayedMessageIds = new Set();
            
            // Track unread sent message IDs (to poll for read status)
            const unreadSentMessageIds = new Set();
            
            // Initialize displayed message IDs and unread sent messages from existing messages
            @foreach($messages as $msg)
                displayedMessageIds.add({{ $msg->id }});
                @if($msg->sender_id === auth()->id() && $msg->read == false)
                    unreadSentMessageIds.add({{ $msg->id }});
                    console.log('Initial unread message:', {{ $msg->id }});
                @endif
            @endforeach

            // DOM elements
            const chatMessages = document.getElementById('chat-messages');
            const menuBtn = document.getElementById('menuBtn');
            const dropdownMenu = document.getElementById('dropdownMenu');

            // Dropdown toggle
            menuBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdownMenu.classList.toggle('show');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!dropdownMenu.contains(e.target) && !menuBtn.contains(e.target)) {
                    dropdownMenu.classList.remove('show');
                }
            });

            // Close dropdown on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    dropdownMenu.classList.remove('show');
                }
            });

            // Auto-scroll to bottom on load
            chatMessages.scrollTop = chatMessages.scrollHeight;

            // Function to update tick color for a message
            function updateTickColor(messageId, isRead) {
                console.log(`updateTickColor called: messageId=${messageId}, isRead=${isRead}`);
                const messageEl = document.querySelector(`[data-message-id="${messageId}"]`);
                console.log('Found message element:', messageEl);
                if (messageEl) {
                    const tickIcon = messageEl.querySelector('.tick-icon');
                    console.log('Found tick icon:', tickIcon);
                    if (tickIcon) {
                        if (isRead) {
                            tickIcon.classList.remove('text-gray-400');
                            tickIcon.classList.add('text-blue-500');
                            // Force style update
                            tickIcon.style.color = '#3b82f6'; // blue-500
                            console.log('Tick updated to BLUE');
                        } else {
                            tickIcon.classList.remove('text-blue-500');
                            tickIcon.classList.add('text-gray-400');
                            tickIcon.style.color = '#9ca3af'; // gray-400
                            console.log('Tick updated to GRAY');
                        }
                    } else {
                        console.log('Tick icon NOT found in message element');
                    }
                } else {
                    console.log(`Message element NOT found for id: ${messageId}`);
                }
            }

            // Function to add message to UI
            function addMessageToUI(message, isSent, messageId = null, isRead = false) {
                // Check if message already displayed
                if (messageId && displayedMessageIds.has(messageId)) {
                    // If it's a sent message, update the tick color if read status changed
                    if (isSent && message.read !== undefined) {
                        updateTickColor(messageId, message.read);
                        if (message.read) {
                            unreadSentMessageIds.delete(messageId);
                        }
                    }
                    return;
                }
                
                if (messageId) {
                    displayedMessageIds.add(messageId);
                    if (messageId > lastMessageId) {
                        lastMessageId = messageId;
                    }
                    // Track unread sent messages
                    if (isSent && !isRead) {
                        unreadSentMessageIds.add(messageId);
                    }
                }

                const tickColor = isRead ? 'text-blue-500' : 'text-gray-400';
                const canDelete = currentUserId == 1;
                const deleteBtnLeft = canDelete && messageId && !isSent ? `
                    <button class="delete-btn self-center mr-1 p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-full transition-all" onclick="deleteMessage(${messageId})" title="Delete message">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                ` : '';
                const deleteBtnRight = canDelete && messageId && isSent ? `
                    <button class="delete-btn self-center ml-1 p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-full transition-all" onclick="deleteMessage(${messageId})" title="Delete message">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                ` : '';
                
                const messageHtml = `
                    <div class="flex ${isSent ? 'justify-end' : 'justify-start'} mb-3 message-container group" data-message-id="${messageId || ''}">
                        ${deleteBtnLeft}
                        <div class="message-bubble ${isSent ? 'message-sent' : 'message-received'}">
                            <span class="text-sm sm:text-base text-gray-800 message-text">${escapeHtml(message.message)}</span>
                            <span class="message-time">
                                ${message.created_at}
                                ${isSent ? `
                                    <svg class="inline w-4 h-4 ml-1 tick-icon ${tickColor}" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M18 7l-1.41-1.41-6.34 6.34 1.41 1.41L18 7zm4.24-1.41L11.66 16.17 7.48 12l-1.41 1.41L11.66 19l12-12-1.42-1.41zM.41 13.41L6 19l1.41-1.41L1.83 12 .41 13.41z"/>
                                    </svg>
                                ` : ''}
                            </span>
                        </div>
                        ${deleteBtnRight}
                    </div>
                `;
                
                chatMessages.insertAdjacentHTML('beforeend', messageHtml);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            // Escape HTML to prevent XSS
            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // Fetch new messages (polling)
            function fetchNewMessages() {
                fetch(`/chat/${chatPartnerId}/messages?last_id=${lastMessageId}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.messages && data.messages.length > 0) {
                        data.messages.forEach(msg => {
                            const isSent = msg.sender_id === currentUserId;
                            addMessageToUI(msg, isSent, msg.id, msg.read || false);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching messages:', error);
                });
            }

            // Fetch read status for unread sent messages
            function fetchReadStatus() {
                if (unreadSentMessageIds.size === 0) return;

                const ids = Array.from(unreadSentMessageIds).join(',');
                console.log('Checking read status for messages:', ids);
                
                fetch(`/chat/${chatPartnerId}/read-status?ids=${ids}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Read status response:', data);
                    if (data.statuses && data.statuses.length > 0) {
                        data.statuses.forEach(status => {
                            console.log(`Message ${status.id} read status:`, status.read);
                            if (status.read) {
                                updateTickColor(status.id, true);
                                unreadSentMessageIds.delete(status.id);
                                console.log(`Updated tick to blue for message ${status.id}`);
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching read status:', error);
                });
            }

            // Start polling for new messages every 2 seconds
            const pollingInterval = setInterval(fetchNewMessages, 2000);
            
            // Start polling for read status every 3 seconds
            const readStatusInterval = setInterval(fetchReadStatus, 3000);

            // Clean up polling when leaving the page
            window.addEventListener('beforeunload', function() {
                clearInterval(pollingInterval);
                clearInterval(readStatusInterval);
            });

            // Handle form submission
            document.getElementById('chat-form').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const messageInput = this.querySelector('input[name="message"]');
                const message = messageInput.value.trim();
                
                if (!message) return;

                // Clear input immediately
                messageInput.value = '';

                // Optimistically add message to UI with Indian Standard Time (gray tick - not read yet)
                const now = new Date();
                const timeStr = now.toLocaleTimeString('en-IN', { 
                    hour: 'numeric', 
                    minute: '2-digit', 
                    hour12: true,
                    timeZone: 'Asia/Kolkata'
                });
                
                // Create a temporary ID for optimistic update
                const tempId = 'temp_' + Date.now();
                
                // Add message with temporary ID
                addMessageToUI({
                    message: message,
                    created_at: timeStr
                }, true, tempId, false); // false = not read yet (gray tick)

                // Send to server
                fetch('/chat/send', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        receiver_id: chatPartnerId,
                        message: message
                    })
                }).then(response => response.json())
                  .then(data => {
                      console.log('Message sent:', data);
                      // Add the real message ID to prevent duplicate from polling
                      if (data.message && data.message.id) {
                          // Update the temporary element with the real message ID
                          const tempEl = document.querySelector(`[data-message-id="${tempId}"]`);
                          if (tempEl) {
                              tempEl.setAttribute('data-message-id', data.message.id);
                          }
                          
                          // Remove temp ID and add real ID to tracking
                          displayedMessageIds.delete(tempId);
                          displayedMessageIds.add(data.message.id);
                          unreadSentMessageIds.add(data.message.id); // Track for read status
                          
                          if (data.message.id > lastMessageId) {
                              lastMessageId = data.message.id;
                          }
                      }
                  })
                  .catch(error => {
                      console.error('Error sending message:', error);
                  });
            });

            console.log('Chat initialized with polling (messages: 2s, read status: 3s)');
            console.log('Unread sent messages being tracked:', Array.from(unreadSentMessageIds));
            
            // Run read status check immediately on load
            setTimeout(fetchReadStatus, 1000);
        });

        // Delete message function (available globally for onclick)
        @if(auth()->id() == 1)
        function deleteMessage(messageId) {
            if (!confirm('Are you sure you want to delete this message?')) {
                return;
            }

            console.log('Deleting message:', messageId);

            fetch(`/chat/message/${messageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    // Remove the message from DOM
                    const messageEl = document.querySelector(`[data-message-id="${messageId}"]`);
                    if (messageEl) {
                        messageEl.style.transition = 'opacity 0.3s, transform 0.3s';
                        messageEl.style.opacity = '0';
                        messageEl.style.transform = 'scale(0.8)';
                        setTimeout(() => messageEl.remove(), 300);
                    }
                } else {
                    alert('Failed to delete message: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error deleting message:', error);
                alert('Failed to delete message: ' + error.message);
            });
        }

        // Delete all messages function
        function deleteAllMessages() {
            if (!confirm('Are you sure you want to delete ALL messages in this conversation? This action cannot be undone.')) {
                return;
            }

            // Close the dropdown menu
            document.getElementById('dropdownMenu').classList.remove('show');

            fetch(`/chat/{{ $user->id }}/delete-all`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove all messages from DOM with animation
                    const chatMessages = document.getElementById('chat-messages');
                    const allMessages = chatMessages.querySelectorAll('.message-container');
                    
                    allMessages.forEach((msg, index) => {
                        setTimeout(() => {
                            msg.style.transition = 'opacity 0.3s, transform 0.3s';
                            msg.style.opacity = '0';
                            msg.style.transform = 'scale(0.8)';
                            setTimeout(() => msg.remove(), 300);
                        }, index * 50); // Stagger the animations
                    });

                    alert(`${data.count} messages deleted successfully.`);
                } else {
                    alert('Failed to delete messages: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error deleting all messages:', error);
                alert('Failed to delete messages');
            });
        }
        @endif
    </script>
</x-app-layout>
