<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Smart Forum Workspace</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Light-blue "jumped to this message" flash — easy on the eyes, fades on its own */
        @keyframes messageHighlightFlash {
            0%   { background-color: rgba(191, 219, 254, 0.9); } /* blue-200 */
            100% { background-color: transparent; }
        }
        .message-highlight {
            animation: messageHighlightFlash 1.8s ease-out;
        }
    </style>
</head>
<body class="bg-gray-100 antialiased font-sans">

<div class="flex h-screen bg-white">
    <!-- Sidebar Left Layout Workspace Pane -->
    <div class="w-64 bg-[#0b1329] text-slate-300 flex flex-col border-r border-slate-900">

        <div class="p-3 bg-[#070d1c] border-b border-slate-900">
           <a href="{{ route('dashboard') }}" class="flex items-center text-gray-400 hover:text-white px-4 py-3 transition">
                <span class="mr-2">←</span> Back to Dashboard
            </a>
        </div>

        <div class="p-4 border-b border-slate-900 flex items-center justify-between">
            <h1 class="text-xs font-bold tracking-wider text-slate-400 uppercase">Workspaces</h1>
            <span class="text-[11px] bg-slate-800 text-slate-300 px-2 py-0.5 rounded-full border border-slate-700 font-mono">
                {{ auth()->user()->username ?? auth()->user()->name }}
            </span>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-6">

            <!-- Global Communication / Main Chat -->
            <div>
                <h2 class="text-[10px] font-bold tracking-wider text-slate-500 uppercase mb-2 flex items-center">
                    <i class="fa-solid fa-earth-americas text-emerald-400 mr-2"></i> Global Communication
                </h2>
                @php
                    $isMainBroadcastActive = ($type === 'broadcast' || !$type || $id === 'general') && !request('topic');
                @endphp
                <a href="{{ url('/forum-workspace') }}"
                   class="flex items-center justify-between px-3 py-2.5 text-xs rounded-md transition-all duration-200 relative group
                   {{ $isMainBroadcastActive
                       ? 'bg-slate-800 text-white font-bold scale-[1.06] shadow-lg shadow-emerald-500/20 border-l-4 border-emerald-400 pl-2 translate-x-1 z-10'
                       : 'hover:bg-slate-800 hover:text-white text-slate-400' }}">
                    <div class="flex items-center space-x-2 truncate">
                        <i class="fa-solid fa-comments {{ $isMainBroadcastActive ? 'text-emerald-400 animate-pulse' : 'text-slate-500 group-hover:text-emerald-400' }} text-sm transition-colors"></i>
                        <span class="truncate">Main Chat Broadcast</span>
                    </div>

                    @if(isset($mainChatUnread) && $mainChatUnread > 0)
                        <span class="bg-red-500 text-white font-bold text-[10px] px-2 py-0.5 rounded-full min-w-[20px] text-center shrink-0 shadow-sm ml-2">
                            {{ $mainChatUnread }}
                        </span>
                    @endif
                </a>
            </div>

            <!-- Active Topics Section Loop -->
            <div x-data="{ openTopics: true }">
                <button
                    @click="openTopics = !openTopics"
                    class="w-full flex items-center justify-between text-[10px] font-bold tracking-wider text-slate-500 uppercase mb-3 focus:outline-none">
                    <span class="flex items-center">
                        <i class="fa-solid fa-graduation-cap text-sky-400 mr-2"></i>
                        Active Topics
                    </span>
                    <i class="fa-solid" :class="openTopics ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </button>

                <ul x-show="openTopics" x-transition class="space-y-2">
                    @foreach($topics as $topic)
                        @php
                            $isActiveTopic = ($type === 'topic' && $id == $topic->id) || (request('topic') == $topic->id);
                        @endphp
                        <li>
                            <a href="{{ url('/forum-workspace/topic/' . $topic->id) }}"
                               class="flex items-center justify-between px-3 py-2.5 text-xs rounded-md transition-all duration-200 relative group
                               {{ $isActiveTopic
                                   ? 'bg-slate-800 text-white font-bold scale-[1.06] shadow-xl shadow-blue-500/25 border-l-4 border-blue-500 pl-2 translate-x-1 z-10'
                                   : 'hover:bg-slate-800 hover:text-white text-slate-400' }}">
                                <div class="flex items-center space-x-2 truncate">
                                    <i class="fa-solid fa-book-bookmark {{ $isActiveTopic ? 'text-blue-400 animate-pulse' : 'text-slate-500 group-hover:text-blue-400' }} transition-colors"></i>
                                    <span class="truncate">
                                        {{ $topic->course_code ?? $topic->title }}
                                    </span>
                                </div>

                                @if(isset($topic->unread_count) && $topic->unread_count > 0)
                                    <span class="bg-red-500 text-white font-bold text-[10px] px-2 py-0.5 rounded-full min-w-[20px] text-center shrink-0 shadow-sm ml-2">
                                        {{ $topic->unread_count }}
                                    </span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Study Groups Section Loop -->
            <div>
                <h2 class="text-[10px] font-bold tracking-wider text-slate-500 uppercase mb-3 flex items-center">
                    <i class="fa-solid fa-users text-purple-400 mr-2"></i> Study Groups
                </h2>
                <ul class="space-y-2">
                    @if(isset($sidebarGroups) && count($sidebarGroups) > 0)
                        @foreach($sidebarGroups as $sGroup)
                            @php
                                $isActiveGroup = ($type === 'group' && $id == $sGroup->id);
                            @endphp
                            <li>
                                <a href="{{ url('/forum-workspace/group/' . $sGroup->id) }}"
                                   class="flex items-center justify-between px-3 py-2.5 text-xs rounded-md transition-all duration-200 relative group
                                   {{ $isActiveGroup
                                       ? 'bg-slate-800 text-white font-bold scale-[1.06] shadow-xl shadow-purple-500/25 border-l-4 border-purple-500 pl-2 translate-x-1 z-10'
                                       : 'hover:bg-slate-800 hover:text-white text-slate-400' }}">
                                    <div class="flex items-center space-x-2 truncate">
                                        <i class="fa-solid fa-hashtag {{ $isActiveGroup ? 'text-purple-400 animate-pulse' : 'text-slate-500 group-hover:text-purple-400' }} transition-colors"></i>
                                        <span class="truncate">{{ $sGroup->name }}</span>
                                    </div>

                                    @if(isset($sGroup->unread_count) && $sGroup->unread_count > 0)
                                        <span class="bg-red-500 text-white font-bold text-[10px] px-2 py-0.5 rounded-full min-w-[20px] text-center shrink-0 shadow-sm ml-2">
                                            {{ $sGroup->unread_count }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    @else
                        <li class="px-3 py-2 text-[11px] text-slate-600 italic">No joined groups</li>
                    @endif
                </ul>
            </div>

        </div>
    </div>

    <!-- Active Right Side Panel Chat Streams Messaging Core Interface -->
    <div class="flex-1 flex flex-col h-full bg-[#f8fafc] relative">

        <div id="chat-messages-container" class="flex-1 overflow-y-auto px-8 py-6 space-y-4 pb-28 scroll-smooth">
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl m-4 shadow-sm flex items-start space-x-2">
                    <i class="fa-solid fa-circle-exclamation mt-0.5 text-red-500"></i>
                    <ul class="list-disc list-inside text-xs font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(count($messages) > 0)
                @foreach($messages as $msg)
                    <div class="flex items-end space-x-3 {{ $msg->user_id === auth()->id() ? 'flex-row-reverse space-x-reverse' : '' }} mb-1"
                         data-message-id="{{ $msg->id }}"
                         data-sender="{{ $msg->user_id === auth()->id() ? 'You' : ($msg->user->name ?? 'Unknown') }}"
                         data-body="{{ $msg->body }}">

                        <div class="h-9 w-9 rounded-full bg-[#0b1329] text-slate-300 flex items-center justify-center text-xs font-bold shrink-0 shadow">
                            @if($msg->user_id === auth()->id())
                                ME
                            @else
                                {{ strtoupper(substr($msg->user->name ?? 'U', 0, 2)) }}
                            @endif
                        </div>

                        <div class="flex flex-col max-w-xl {{ $msg->user_id === auth()->id() ? 'items-end' : 'items-start' }}">
                            <div class="message-bubble relative px-4 py-2.5 shadow-md rounded-xl break-words w-full border
                                {{ $msg->user_id === auth()->id()
                                    ? 'bg-sky-100 text-slate-900 border-sky-200 rounded-tr-none'
                                    : 'bg-white text-slate-800 border-slate-100 rounded-tl-none' }}">

                                @if($msg->user_id !== auth()->id())
                                    <div class="text-[11px] font-bold text-emerald-600 mb-1 tracking-wide uppercase">
                                        {{ $msg->user->name }}
                                    </div>
                                @endif

                                {{-- WhatsApp-style quoted reply block --}}
                                @if($msg->replyTo)
                                    <button type="button"
                                            class="quote-jump w-full text-left mb-1.5 pl-2 pr-2 py-1 border-l-4 border-sky-400 bg-sky-50 hover:bg-sky-100 rounded-md text-xs transition-colors"
                                            data-jump-id="{{ $msg->reply_to_message_id }}">
                                        <div class="font-bold text-sky-700 text-[10px] uppercase">
                                            {{ $msg->replyTo->user_id === auth()->id() ? 'You' : ($msg->replyTo->user->name ?? 'Unknown') }}
                                        </div>
                                        <div class="text-slate-600 truncate max-w-[260px]">
                                            {{ Str::limit($msg->replyTo->body, 60) }}
                                        </div>
                                    </button>
                                @elseif($msg->reply_to_message_id)
                                    <div class="mb-1.5 pl-2 pr-2 py-1 border-l-4 border-slate-300 bg-black/5 rounded-md text-xs italic text-slate-400">
                                        Original message deleted
                                    </div>
                                @endif

                                <div class="pr-14 text-sm font-normal leading-relaxed">
                                    {{ $msg->body }}
                                </div>

                                <div class="mt-2 pt-1 border-t border-slate-200/60 flex items-center justify-between space-x-2">
                                    <button
                                        type="button"
                                        class="reply-trigger text-[10px] font-semibold text-blue-600 hover:text-blue-800 transition-colors flex items-center space-x-1">
                                        <i class="fa-solid fa-reply text-[9px]"></i>
                                        <span>Reply</span>
                                    </button>

                                    {{-- Delete Option for Owner --}}
                                    @if($msg->user_id === auth()->id())
                                        <form action="{{ route('chat.destroy', $msg->id) }}" method="POST" onsubmit="return confirm('Delete this message?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-[10px] font-semibold text-red-500 hover:text-red-700 transition-colors flex items-center space-x-1">
                                                <i class="fa-solid fa-trash-can text-[9px]"></i>
                                                <span>Delete</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                <span class="absolute bottom-1 right-2.5 text-[10px] select-none font-mono
                                    {{ $msg->user_id === auth()->id() ? 'text-slate-500' : 'text-gray-400' }}">
                                    {{ $msg->created_at ? $msg->created_at->format('H:i') : now()->format('H:i') }}
                                    @if($msg->user_id === auth()->id())
                                        <i class="fa-solid fa-check-double text-sky-600 ml-0.5"></i>
                                    @endif
                                </span>
                            </div>
                        </div>

                    </div>
                @endforeach
            @else
                <div id="fallback-empty" class="h-full flex flex-col items-center justify-center text-slate-400 p-8 text-center">
                    <div class="w-16 h-16 rounded-full bg-[#0b1329] flex items-center justify-center shadow mb-4">
                        <i class="fa-solid fa-graduation-cap text-slate-300 text-2xl"></i>
                    </div>
                    <h2 class="text-sm font-bold text-slate-700">Course Discussion Hub</h2>
                    <p class="text-xs mt-1 max-w-xs text-slate-500">Select a course code thread stream from the menu to open historical records.</p>
                </div>
            @endif
        </div>

        <!-- Sticky Bottom Chat Input Container -->
        <div class="absolute bottom-0 left-0 right-0 p-4 bg-slate-50 border-t border-slate-200 z-10">
            <form id="chat-form" method="POST" action="{{ route('chat.store', ['type' => $type ?? (request('topic') ? 'topic' : 'broadcast'), 'id' => $id ?? (request('topic') ?: 'general')]) }}" class="flex flex-col space-y-2 max-w-7xl mx-auto">
                @csrf
                <input type="hidden" id="reply_to_message_id" name="reply_to_message_id" value="">
                <input type="hidden" id="restrict_course_id" name="restrict_course_id" value="">

                <!-- Replying State Preview Bar -->
                <div id="reply-box" class="hidden text-xs bg-sky-50 border border-sky-200 text-sky-800 px-3 py-2 rounded-xl flex items-center justify-between shadow-sm">
                    <div class="truncate pr-2">
                        <span id="reply-sender" class="font-bold text-sky-700"></span>
                        <span id="reply-text" class="italic ml-1 text-slate-700"></span>
                    </div>
                    <button type="button" id="cancel-reply-btn" class="text-red-500 hover:text-red-700 font-bold text-xs shrink-0 px-1">
                        <i class="fa-solid fa-xmark mr-1"></i>Cancel
                    </button>
                </div>

                <div class="flex items-center space-x-3 w-full">
                    <div class="relative inline-block text-left">
                        <button type="button" id="dropdown-toggle" class="flex items-center space-x-2 px-3 py-2 bg-white border border-slate-300 rounded-xl text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none transition-all cursor-pointer">
                            <i class="fa-solid fa-filter text-xs text-sky-500"></i>
                            <span id="restrict-label" class="text-[11px] font-semibold tracking-wide max-w-[110px] truncate">All Courses</span>
                            <i class="fa-solid fa-chevron-down text-[9px] text-slate-400"></i>
                        </button>

                        <div id="dropdown-menu" class="hidden absolute bottom-full mb-2 left-0 w-64 bg-white rounded-xl shadow-xl border border-slate-200 overflow-hidden divide-y divide-slate-100 z-50">
                            <div class="py-1">
                                <span class="block px-3 py-1.5 text-[9px] font-bold text-slate-400 uppercase tracking-wider">
                                    Restrict By Course
                                </span>

                                <button
                                    type="button"
                                    id="clear-restrict"
                                    class="w-full text-left block px-3 py-2 text-xs text-slate-600 hover:bg-slate-50 font-medium">
                                    <i class="fa-solid fa-xmark mr-1.5 text-slate-400"></i> All Courses
                                </button>

                                @foreach($courses as $course)
                                    <button
                                        type="button"
                                        class="course-select w-full text-left block px-3 py-2 text-xs text-slate-700 hover:bg-sky-50 transition-colors"
                                        data-course="{{ $course->courseCode }}"
                                        data-label="{{ $course->courseCode }}">

                                        <span class="bg-sky-100 text-sky-800 font-mono text-[10px] px-1.5 py-0.5 rounded mr-1.5 border border-sky-200 font-semibold">
                                            {{ $course->courseCode }}
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @if($currentStudent && $currentStudent->status === 'blacklisted')
                        <div class="flex-1 flex items-center justify-center py-2.5 bg-red-50 border border-red-200 rounded-xl text-red-700 text-xs font-medium shadow-sm">
                            <i class="fa-solid fa-circle-exclamation text-red-500 mr-2"></i>
                            <span>Your messaging privileges have been suspended due to inactivity.</span>
                        </div>
                    @else
                        <div class="flex-1 relative flex items-center">
                            <input type="text" id="message-input" name="body" required autocomplete="off" placeholder="Write an answer or update workspace thread..."
                                   class="w-full text-sm px-4 py-2.5 bg-white rounded-xl focus:outline-none focus:ring-1 focus:ring-slate-800 transition-all text-slate-800 placeholder-slate-400 shadow-sm border border-slate-200">
                        </div>

                        <button type="submit" id="send-btn" class="w-10 h-10 bg-[#0b1329] hover:bg-slate-800 text-slate-200 rounded-full transition-all shadow active:scale-95 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-paper-plane text-xs"></i>
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<script type="module">
    const container = document.getElementById('chat-messages-container');
    let fallbackEmpty = document.getElementById('fallback-empty');
    const currentUserId = {{ auth()->id() }};
    const currentUserName = "{{ auth()->user()->name }}";
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // ===============================
    // Message cache
    // ===============================
    const messageCache = {};
    document.querySelectorAll('[data-message-id]').forEach(el => {
        messageCache[el.dataset.messageId] = {
            sender: el.dataset.sender,
            body: el.dataset.body,
        };
    });

    const escapeHtml = (str) => {
        if (typeof str !== 'string') return '';
        return str
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    };

    // ===============================
    // Reply box controls
    // ===============================
    const replyBox = document.getElementById('reply-box');
    const replySender = document.getElementById('reply-sender');
    const replyText = document.getElementById('reply-text');
    const replyToIdInput = document.getElementById('reply_to_message_id');
    const messageInput = document.getElementById('message-input');

    function setReply(id, sender, body) {
        replyToIdInput.value = id;
        replySender.textContent = sender + ':';
        replyText.textContent = body.length > 80 ? body.slice(0, 80) + '…' : body;
        replyBox.classList.remove('hidden');
        messageInput.focus();
    }

    function cancelReply() {
        replyToIdInput.value = '';
        replyBox.classList.add('hidden');
        replySender.textContent = '';
        replyText.textContent = '';
    }

    document.getElementById('cancel-reply-btn').addEventListener('click', cancelReply);

    // Event Delegation
    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('.reply-trigger');
        if (trigger) {
            const row = trigger.closest('[data-message-id]');
            if (!row) return;
            setReply(row.dataset.messageId, row.dataset.sender, row.dataset.body);
            return;
        }

        const quote = e.target.closest('.quote-jump');
        if (quote) {
            const target = document.querySelector(`[data-message-id="${quote.dataset.jumpId}"]`);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'center' });

                const bubble = target.querySelector('.message-bubble');
                if (bubble) {
                    bubble.classList.remove('message-highlight');
                    void bubble.offsetWidth;
                    bubble.classList.add('message-highlight');
                    setTimeout(() => bubble.classList.remove('message-highlight'), 1800);
                }
            }
        }
    });

    // ===============================
    // Restrict dropdown
    // ===============================
    const toggleBtn = document.getElementById('dropdown-toggle');
    const menuEl = document.getElementById('dropdown-menu');
    const restrictLabel = document.getElementById('restrict-label');
    const restrictInput = document.getElementById('restrict_course_id');

    if (toggleBtn && menuEl) {
        toggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            menuEl.classList.toggle('hidden');
        });
        document.addEventListener('click', () => menuEl.classList.add('hidden'));
        menuEl.addEventListener('click', (e) => e.stopPropagation());
    }

    function selectCourse(id, label) {
        restrictInput.value = id || '';
        restrictLabel.textContent = label;

        document.querySelectorAll('.course-select').forEach(btn => {
            const isSelected = String(id) === btn.dataset.course;
            btn.classList.toggle('bg-sky-50', isSelected);
            btn.classList.toggle('font-semibold', isSelected);
        });

        menuEl.classList.add('hidden');
    }

    document.querySelectorAll('.course-select').forEach(btn => {
        btn.addEventListener('click', () => selectCourse(btn.dataset.course, btn.dataset.label));
    });

    const clearBtn = document.getElementById('clear-restrict');
    if (clearBtn) {
        clearBtn.addEventListener('click', () => selectCourse('', 'All Courses'));
    }

    // ===============================
    // Scrolling
    // ===============================
    const scrollToBottom = () => {
        if (container) container.scrollTop = container.scrollHeight;
    };
    scrollToBottom();

    // ===============================
    // Append a message DOM row
    // ===============================
    const appendNewMessage = (body, senderName, senderId, messageId, replyTo = null) => {
        if (!container) return;
        if (fallbackEmpty) { fallbackEmpty.remove(); fallbackEmpty = null; }

        const isMe = parseInt(senderId) === currentUserId;
        const avatarDisplay = isMe ? "ME" : senderName.substring(0, 2).toUpperCase();
        const timeStr = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });

        const replyBlock = replyTo ? `
            <button type="button" class="quote-jump w-full text-left mb-1.5 pl-2 pr-2 py-1 border-l-4 border-sky-400 bg-sky-50 hover:bg-sky-100 rounded-md text-xs transition-colors" data-jump-id="${replyTo.id}">
                <div class="font-bold text-sky-700 text-[10px] uppercase">${escapeHtml(replyTo.sender)}</div>
                <div class="text-slate-600 truncate max-w-[260px]">${escapeHtml(replyTo.body)}</div>
            </button>` : '';

        const deleteForm = (isMe && messageId) ? `
            <form action="/chat/messages/${messageId}" method="POST" onsubmit="return confirm('Delete this message?');" class="inline">
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" class="text-[10px] font-semibold text-red-500 hover:text-red-700 transition-colors flex items-center space-x-1">
                    <i class="fa-solid fa-trash-can text-[9px]"></i>
                    <span>Delete</span>
                </button>
            </form>` : '';

        const messageRow = document.createElement('div');
        messageRow.className = `flex items-end space-x-3 ${isMe ? 'flex-row-reverse space-x-reverse' : ''} mb-1`;
        if (messageId) messageRow.dataset.messageId = messageId;
        messageRow.dataset.sender = isMe ? 'You' : senderName;
        messageRow.dataset.body = body;

        messageRow.innerHTML = `
            <div class="h-9 w-9 rounded-full bg-[#0b1329] text-slate-300 flex items-center justify-center text-xs font-bold shrink-0 shadow">
                ${avatarDisplay}
            </div>
            <div class="flex flex-col max-w-xl ${isMe ? 'items-end' : 'items-start'}">
                <div class="message-bubble relative px-4 py-2.5 shadow-sm rounded-xl break-words w-full border
                    ${isMe ? 'bg-sky-100 text-slate-900 border-sky-200 rounded-tr-none' : 'bg-white text-slate-800 border-slate-100 rounded-tl-none'}">
                    ${!isMe ? `<div class="text-[11px] font-bold text-emerald-600 mb-1 tracking-wide uppercase">${escapeHtml(senderName)}</div>` : ''}
                    ${replyBlock}
                    <div class="pr-14 text-sm font-normal leading-relaxed">${escapeHtml(body)}</div>
                    <div class="mt-2 pt-1 border-t border-slate-200/60 flex items-center justify-between space-x-2">
                        <button type="button" class="reply-trigger text-[10px] font-semibold text-blue-600 hover:text-blue-800 transition-colors flex items-center space-x-1">
                            <i class="fa-solid fa-reply text-[9px]"></i><span>Reply</span>
                        </button>
                        ${deleteForm}
                    </div>
                    <span class="absolute bottom-1 right-2.5 text-[10px] select-none font-mono ${isMe ? 'text-slate-500' : 'text-gray-400'}">
                        ${timeStr}
                        ${isMe ? '<i class="fa-solid fa-check-double text-sky-600 ml-0.5"></i>' : ''}
                    </span>
                </div>
            </div>
        `;

        container.appendChild(messageRow);
        scrollToBottom();

        if (messageId) {
            messageCache[messageId] = { sender: isMe ? 'You' : senderName, body };
        }

        return messageRow;
    };

    // ===============================
    // SEND MESSAGE
    // ===============================
    const chatForm = document.getElementById('chat-form');
    if (chatForm) {
        chatForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const messageText = messageInput.value.trim();
            if (!messageText) return;

            const formData = new FormData(this);
            const targetUrl = this.getAttribute('action');
            const sendBtn = document.getElementById('send-btn');

            const replyId = replyToIdInput.value || null;
            const replyToData = replyId ? messageCache[replyId] : null;

            if (sendBtn) sendBtn.disabled = true;

            const createdRow = appendNewMessage(messageText, currentUserName, currentUserId, null, replyToData);

            messageInput.value = '';
            cancelReply();

            fetch(targetUrl, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                },
            })
                .then(async (response) => {
                    if (!response.ok) {
                        const text = await response.text();
                        throw new Error(`Server rejected message (${response.status}): ${text}`);
                    }
                    return response.json();
                })
                .then((data) => {
                    if (data?.message?.id) {
                        messageCache[data.message.id] = { sender: 'You', body: messageText };
                        if (createdRow) {
                            createdRow.dataset.messageId = data.message.id;
                            // Attach delete form dynamically now that we have the real message ID
                            const actionBox = createdRow.querySelector('.border-t');
                            if (actionBox && !actionBox.querySelector('form')) {
                                actionBox.insertAdjacentHTML('beforeend', `
                                    <form action="/chat/messages/${data.message.id}" method="POST" onsubmit="return confirm('Delete this message?');" class="inline">
                                        <input type="hidden" name="_token" value="${csrfToken}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="text-[10px] font-semibold text-red-500 hover:text-red-700 transition-colors flex items-center space-x-1">
                                            <i class="fa-solid fa-trash-can text-[9px]"></i>
                                            <span>Delete</span>
                                        </button>
                                    </form>
                                `);
                            }
                        }
                    }
                })
                .catch((error) => {
                    console.error('Sending failed:', error);
                    alert('Your message failed to send. Please try again.');
                })
                .finally(() => {
                    if (sendBtn) sendBtn.disabled = false;
                });
        });
    }

    // ===============================
    // REVERB / ECHO LISTENER
    // ===============================
    const queryTopicId = "{{ request('topic') }}";
    const currentChannelType = queryTopicId ? "topic" : "{{ $type ?? 'broadcast' }}";
    const currentChannelId = queryTopicId ? queryTopicId : "{{ $id ?? 'general' }}";

    if (window.Echo) {
        window.Echo.channel(`chat.${currentChannelType}.${currentChannelId}`)
            .listen('.MessageSent', (e) => {
                if (parseInt(e.message.user_id) === currentUserId) return;

                const senderName = e.message.user?.name ?? 'Peer User';
                let replyTo = null;

                if (e.message.reply_to) {
                    replyTo = {
                        id: e.message.reply_to.id,
                        sender: e.message.reply_to.user_name ?? 'Unknown',
                        body: e.message.reply_to.body ?? '',
                    };
                } else if (
                    e.message.reply_to_message_id &&
                    messageCache[e.message.reply_to_message_id]
                ) {
                    replyTo = {
                        id: e.message.reply_to_message_id,
                        sender: messageCache[e.message.reply_to_message_id].sender,
                        body: messageCache[e.message.reply_to_message_id].body,
                    };
                }

                appendNewMessage(
                    e.message.body,
                    senderName,
                    e.message.user_id,
                    e.message.id,
                    replyTo
                );
            });
    }
</script>
</body>
</html>