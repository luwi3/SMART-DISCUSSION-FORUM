<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Smart Forum Workspace</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 antialiased font-sans">

<div class="flex h-screen bg-white">
    <div class="w-64 bg-[#0b1329] text-slate-300 flex flex-col border-r border-slate-900">
        
        <div class="p-3 bg-[#070d1c] border-b border-slate-900">
            <a href="{{ url('/student/dashboard') }}" class="flex items-center space-x-2 text-xs font-semibold text-slate-400 hover:text-sky-400 transition-colors group">
                <i class="fa-solid fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
                <span>Back to Student Dashboard</span>
            </a>
        </div>

        <div class="p-4 border-b border-slate-900 flex items-center justify-between">
            <h1 class="text-xs font-bold tracking-wider text-slate-400 uppercase">Workspaces</h1>
            <span class="text-[11px] bg-slate-800 text-slate-300 px-2 py-0.5 rounded-full border border-slate-700 font-mono">
                {{ auth()->user()->username ?? auth()->user()->name }}
            </span>
        </div>
        
        <div class="flex-1 overflow-y-auto p-4 space-y-6">
            
            <div>
                <h2 class="text-[10px] font-bold tracking-wider text-slate-500 uppercase mb-2 flex items-center">
                    <i class="fa-solid fa-earth-americas text-emerald-400 mr-2"></i> Global Communication
                </h2>
                @php
                    // Main chat is active if type is broadcast, or if no explicit topic route/query is present
                    $isMainBroadcastActive = ($type === 'broadcast' || !$type || $id === 'general') && !request('topic');
                @endphp
                <a href="{{ url('/forum-workspace') }}" 
                   class="flex items-center space-x-2 px-3 py-2.5 text-xs rounded-md transition-all duration-200 relative group
                   {{ $isMainBroadcastActive 
                       ? 'bg-slate-800 text-white font-bold scale-[1.06] shadow-lg shadow-emerald-500/20 border-l-4 border-emerald-400 pl-2 translate-x-1 z-10' 
                       : 'hover:bg-slate-800 hover:text-white text-slate-400' }}">
                    <i class="fa-solid fa-comments {{ $isMainBroadcastActive ? 'text-emerald-400 animate-pulse' : 'text-slate-500 group-hover:text-emerald-400' }} text-sm transition-colors"></i>
                    <span class="truncate">Main Chat Broadcast</span>
                </a>
            </div>

            <div x-data="{ openTopics: true }">

    <button
        @click="openTopics = !openTopics"
        class="w-full flex items-center justify-between text-[10px] font-bold tracking-wider text-slate-500 uppercase mb-3">

        <span class="flex items-center">
            <i class="fa-solid fa-graduation-cap text-sky-400 mr-2"></i>
            Active Topics
        </span>

        <i class="fa-solid"
           :class="openTopics ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
    </button>

    <ul x-show="openTopics"
        x-transition
        class="space-y-2">

        @foreach($topics as $topic)
            @php
                $isActiveTopic = ($type === 'topic' && $id == $topic->id) || (request('topic') == $topic->id);
            @endphp

            <li>
                <a href="{{ url('/forum-workspace/topic/' . $topic->id) }}"
                   class="flex items-center space-x-2 px-3 py-2.5 text-xs rounded-md transition-all duration-200 relative group
                   {{ $isActiveTopic
                       ? 'bg-slate-800 text-white font-bold scale-[1.06] shadow-xl shadow-blue-500/25 border-l-4 border-blue-500 pl-2 translate-x-1 z-10'
                       : 'hover:bg-slate-800 hover:text-white text-slate-400' }}">

                    <i class="fa-solid fa-book-bookmark {{ $isActiveTopic ? 'text-blue-400 animate-pulse' : 'text-slate-500 group-hover:text-blue-400' }} transition-colors"></i>

                    <span class="truncate">
                        {{ $topic->course_code ?? $topic->title }}
                    </span>

                </a>
            </li>
        @endforeach

    </ul>

</div>
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
                                   class="flex items-center space-x-2 px-3 py-2.5 text-xs rounded-md transition-all duration-200 relative group
                                   {{ $isActiveGroup 
                                       ? 'bg-slate-800 text-white font-bold scale-[1.06] shadow-xl shadow-purple-500/25 border-l-4 border-purple-500 pl-2 translate-x-1 z-10' 
                                       : 'hover:bg-slate-800 hover:text-white text-slate-400' }}">
                                    
                                    <i class="fa-solid fa-hashtag {{ $isActiveGroup ? 'text-purple-400 animate-pulse' : 'text-slate-500 group-hover:text-purple-400' }} transition-colors"></i>
                                    <span class="truncate">{{ $sGroup->name }}</span>
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

    <div class="flex-1 flex flex-col h-full bg-[#f8fafc] relative">

        @php
            // Requirement #12: this view is reachable either via a direct
            // /forum-workspace/topic/{id} path, or via a ?topic= query param
            // on the general workspace route - handle both the same way.
            $shareTopicId = null;
            if ($type === 'topic' && $id) {
                $shareTopicId = $id;
            } elseif (request('topic')) {
                $shareTopicId = request('topic');
            }
        @endphp

        @if($shareTopicId && isset($currentStreamTarget) && !($currentStreamTarget->is_broadcast ?? false))
            <div class="px-8 py-3 border-b border-slate-200 bg-white flex items-center justify-between shrink-0">
                <div class="flex items-center space-x-2 min-w-0">
                    <i class="fa-solid fa-book-bookmark text-blue-400 shrink-0"></i>
                    <h2 class="text-sm font-bold text-slate-800 truncate">{{ $currentStreamTarget->title ?? 'Discussion Topic' }}</h2>
                </div>

                <div class="relative inline-block text-left shrink-0" id="share-wrapper">
                    <button type="button" id="share-toggle" class="flex items-center space-x-1.5 px-3 py-1.5 bg-slate-100 border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-200 transition-all text-xs font-semibold">
                        <i class="fa-solid fa-share-nodes text-slate-500"></i>
                        <span>Share</span>
                    </button>

                    <div id="share-menu" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-slate-200 overflow-hidden divide-y divide-slate-100 z-50">
                        <a href="#" data-share="whatsapp" class="flex items-center space-x-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                            <i class="fa-brands fa-whatsapp text-green-500 w-4"></i><span>WhatsApp</span>
                        </a>
                        <a href="#" data-share="twitter" class="flex items-center space-x-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                            <i class="fa-brands fa-x-twitter text-slate-900 w-4"></i><span>X (Twitter)</span>
                        </a>
                        <a href="#" data-share="facebook" class="flex items-center space-x-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                            <i class="fa-brands fa-facebook text-blue-600 w-4"></i><span>Facebook</span>
                        </a>
                        <a href="#" data-share="telegram" class="flex items-center space-x-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                            <i class="fa-brands fa-telegram text-sky-500 w-4"></i><span>Telegram</span>
                        </a>
                        <a href="#" data-share="email" class="flex items-center space-x-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                            <i class="fa-solid fa-envelope text-slate-500 w-4"></i><span>Email</span>
                        </a>
                        <button type="button" id="copy-link-btn" class="w-full flex items-center space-x-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 text-left">
                            <i class="fa-solid fa-link text-slate-500 w-4"></i><span id="copy-link-label">Copy Link</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <div id="chat-messages-container" class="flex-1 overflow-y-auto px-8 py-6 space-y-4 pb-24 scroll-smooth">
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
                    <div class="flex items-end space-x-3 {{ $msg->user_id === auth()->id() ? 'flex-row-reverse space-x-reverse' : '' }} mb-1">
                        
                        <div class="h-9 w-9 rounded-full bg-[#0b1329] text-slate-300 flex items-center justify-center text-xs font-bold shrink-0 shadow">
                            @if($msg->user_id === auth()->id())
                                ME
                            @else
                                {{ strtoupper(substr($msg->user->name ?? 'U', 0, 2)) }}
                            @endif
                        </div>
                        
                        <div class="flex flex-col max-w-xl {{ $msg->user_id === auth()->id() ? 'items-end' : 'items-start' }}">
                            <div class="relative px-4 py-2.5 shadow-md rounded-xl break-words w-full border
                                {{ $msg->user_id === auth()->id() 
                                    ? 'bg-sky-100 text-slate-900 border-sky-200 rounded-tr-none' 
                                    : 'bg-white text-slate-800 border-slate-100 rounded-tl-none' }}">
                                
                                @if($msg->user_id !== auth()->id())
                                    <div class="text-[11px] font-bold text-emerald-600 mb-1 tracking-wide uppercase">
                                        {{ $msg->user->name }}
                                    </div>
                                @endif

                                <div class="pr-14 text-sm font-normal leading-relaxed">
                                    {{ $msg->body }}
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

        <div class="absolute bottom-0 left-0 right-0 p-4 bg-slate-50 border-t border-slate-200 z-10">
            <form id="chat-form" method="POST" action="{{ route('chat.store', ['type' => $type ?? (request('topic') ? 'topic' : 'broadcast'), 'id' => $id ?? (request('topic') ?: 'general')]) }}" class="flex items-center space-x-3 max-w-7xl mx-auto">
                @csrf
                
                <div class="relative inline-block text-left">
                    <button type="button" id="dropdown-toggle" class="flex items-center space-x-1.5 px-3 py-2 bg-[#0b1329] border border-slate-950 rounded-xl text-slate-300 shadow-sm focus:outline-none hover:bg-slate-800 transition-all cursor-pointer">
                        <i class="fa-solid fa-shield-halved text-xs text-sky-400"></i>
                        <span class="text-[10px] font-bold tracking-wide uppercase">Restrict</span>
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <i class="fa-solid fa-chevron-up text-[9px] text-slate-400 ml-0.5"></i>
                    </button>

                    <div id="dropdown-menu" class="hidden absolute bottom-full mb-2 left-0 w-64 bg-white rounded-xl shadow-xl border border-slate-200 overflow-hidden divide-y divide-slate-100 z-50">
                        <div class="py-1">
                            <span class="block px-3 py-1.5 text-[9px] font-bold text-slate-400 uppercase tracking-wider">Filter By Course Code</span>
                            @foreach($topics as $topic)
                                @php
                                    $isDropdownActive = ($type === 'topic' && $id == $topic->id) || (request('topic') == $topic->id);
                                @endphp
                                <a href="{{ url('/forum-workspace/topic/' . $topic->id) }}" class="block px-3 py-2 text-xs text-slate-700 hover:bg-slate-50 hover:text-blue-600 truncate {{ $isDropdownActive ? 'bg-blue-50 text-blue-600 font-semibold' : '' }}">
                                    <span class="bg-blue-100 text-blue-800 font-mono text-[10px] px-1.5 py-0.5 rounded mr-1.5 border border-blue-200 font-semibold">
                                        {{ $topic->course_code ?? 'CODE' }}
                                    </span>
                                    <span class="text-slate-500 text-[11px]">{{ $topic->title }}</span>
                                </a>
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
            </form>
        </div>
    </div>
</div>

<script type="module">
    const container = document.getElementById('chat-messages-container');
    const fallbackEmpty = document.getElementById('fallback-empty');
    const currentUserId = {{ auth()->id() }};

    const toggleBtn = document.getElementById('dropdown-toggle');
    const menuEl = document.getElementById('dropdown-menu');
    
    if (toggleBtn && menuEl) {
        toggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            menuEl.classList.toggle('hidden');
        });
        document.addEventListener('click', () => {
            menuEl.classList.add('hidden');
        });
    }

    const scrollToBottom = () => {
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    };

    scrollToBottom();

    const appendNewMessage = (body, senderName, senderId) => {
        if (fallbackEmpty) fallbackEmpty.remove();

        const isMe = (parseInt(senderId) === currentUserId);
        const avatarDisplay = isMe ? "ME" : senderName.substring(0, 2).toUpperCase();
        const timeStr = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });

        const messageRow = document.createElement('div');
        messageRow.className = `flex items-end space-x-3 ${isMe ? 'flex-row-reverse space-x-reverse' : ''} mb-1`;

        messageRow.innerHTML = `
            <div class="h-9 w-9 rounded-full bg-[#0b1329] text-slate-300 flex items-center justify-center text-xs font-bold shrink-0 shadow">
                ${avatarDisplay}
            </div>
            <div class="flex flex-col max-w-xl ${isMe ? 'items-end' : 'items-start'}">
                <div class="relative px-4 py-2.5 shadow-sm rounded-xl break-words w-full border
                    ${isMe ? 'bg-sky-100 text-slate-900 border-sky-200 rounded-tr-none' : 'bg-white text-slate-800 border-slate-100 rounded-tl-none'}">
                    
                    ${!isMe ? `<div class="text-[11px] font-bold text-emerald-600 mb-1 tracking-wide uppercase">${senderName}</div>` : ''}

                    <div class="pr-14 text-sm font-normal leading-relaxed">
                        ${escapeHtml(body)}
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
    };

    const escapeHtml = (str) => {
        return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    };

    const chatForm = document.getElementById('chat-form');
    if (chatForm) {
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault(); 
            
            const input = document.getElementById('message-input');
            const messageText = input.value.trim();
            if (!messageText) return;

            const formData = new FormData(this);
            const targetUrl = this.getAttribute('action'); 
            const sendBtn = document.getElementById('send-btn');
            
            if (sendBtn) sendBtn.disabled = true;

            appendNewMessage(messageText, "{{ auth()->user()->name }}", currentUserId);
            input.value = ''; 

            fetch(targetUrl, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin', 
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .catch(error => { console.error('Submission processing failure:', error); })
            .finally(() => { if (sendBtn) sendBtn.disabled = false; });
        });
    }

    // Normalized parsing mechanics to capture parameters from direct paths OR query fragments
    const queryTopicId = "{{ request('topic') }}";
    const currentChannelType = queryTopicId ? "topic" : "{{ $type ?? 'broadcast' }}";
    const currentChannelId = queryTopicId ? queryTopicId : "{{ $id ?? 'general' }}";

    if (window.Echo) {
        window.Echo.channel(`chat.${currentChannelType}.${currentChannelId}`)
            .listen('MessageSent', (e) => {
                if (parseInt(e.message.user_id) !== currentUserId) {
                    const body = e.message.body;
                    const senderName = e.message.user ? e.message.user.name : 'Peer User';
                    const senderId = e.message.user_id;
                    
                    appendNewMessage(body, senderName, senderId);
                }
            });
    }
</script>

{{-- Requirement #12: builds real share links for the currently open topic.
     Self-contained, doesn't touch any of the chat/websocket logic above. --}}
@if($shareTopicId && isset($currentStreamTarget) && !($currentStreamTarget->is_broadcast ?? false))
<script>
(function() {
    const shareToggle = document.getElementById('share-toggle');
    const shareMenu = document.getElementById('share-menu');

    if (shareToggle && shareMenu) {
        shareToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            shareMenu.classList.toggle('hidden');
        });
        document.addEventListener('click', function() {
            shareMenu.classList.add('hidden');
        });
        shareMenu.addEventListener('click', function(e) { e.stopPropagation(); });
    }

    // Always share the canonical /forum-workspace/topic/{id} URL, regardless
    // of whether this page was reached via that path or via a ?topic= query.
    const topicUrl = "{{ url('/forum-workspace/topic/' . $shareTopicId) }}";
    const topicTitle = @json($currentStreamTarget->title ?? 'Discussion Topic');
    const shareText = topicTitle + ' - Join the discussion:';

    const shareLinks = {
        whatsapp: 'https://wa.me/?text=' + encodeURIComponent(shareText + ' ' + topicUrl),
        twitter: 'https://twitter.com/intent/tweet?text=' + encodeURIComponent(shareText) + '&url=' + encodeURIComponent(topicUrl),
        facebook: 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(topicUrl),
        telegram: 'https://t.me/share/url?url=' + encodeURIComponent(topicUrl) + '&text=' + encodeURIComponent(shareText),
        email: 'mailto:?subject=' + encodeURIComponent(topicTitle) + '&body=' + encodeURIComponent(shareText + ' ' + topicUrl)
    };

    document.querySelectorAll('[data-share]').forEach(function(link) {
        const platform = link.getAttribute('data-share');
        if (shareLinks[platform]) {
            link.setAttribute('href', shareLinks[platform]);
            link.setAttribute('target', '_blank');
            link.setAttribute('rel', 'noopener noreferrer');
        }
    });

    const copyBtn = document.getElementById('copy-link-btn');
    const copyLabel = document.getElementById('copy-link-label');
    if (copyBtn) {
        copyBtn.addEventListener('click', function() {
            navigator.clipboard.writeText(topicUrl).then(function() {
                if (copyLabel) {
                    const original = copyLabel.textContent;
                    copyLabel.textContent = 'Copied!';
                    setTimeout(function() { copyLabel.textContent = original; }, 1500);
                }
            });
        });
    }
})();
</script>
@endif
</body>
</html>