<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Smart Forum Workspace</title>
    
    <!-- Tailwind CSS CDN -->
   @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 antialiased font-sans">

<div class="flex h-screen bg-white">
    <!-- Sidebar Workspace Navigation Container -->
    <div class="w-64 bg-[#0b1329] text-slate-300 flex flex-col border-r border-slate-900">
        <div class="p-4 border-b border-slate-900 flex items-center justify-between">
            <h1 class="text-xs font-bold tracking-wider text-slate-400 uppercase">Workspaces</h1>
            <span class="text-[11px] bg-slate-800 text-slate-300 px-2 py-0.5 rounded-full border border-slate-700 font-mono">
                {{ auth()->user()->username ?? auth()->user()->name }}
            </span>
        </div>
        
        <div class="flex-1 overflow-y-auto p-4 space-y-6">
            <!-- Active Courses Sidebar Collection Container -->
            <div>
                <h2 class="text-[10px] font-bold tracking-wider text-slate-500 uppercase mb-2 flex items-center">
                    <i class="fa-solid fa-graduation-cap text-sky-400 mr-2"></i> Active Courses
                </h2>
                <ul class="space-y-1">
                    @foreach($topics as $topic)
                        <li>
                            <a href="{{ url('/forum-workspace/topic/' . $topic->id) }}" 
                               class="flex items-center space-x-2 px-2 py-1.5 text-xs rounded-md transition-colors hover:bg-slate-800 hover:text-white 
                               {{ ($type === 'topic' && $id == $topic->id) ? 'bg-slate-800 text-white font-medium' : '' }}">
                                <i class="fa-solid fa-book-bookmark text-blue-400"></i>
                                <span class="truncate">{{ $topic->course_code ?? $topic->title }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Recent Group Discussions Sidebar Collection Container -->
            <div>
                <h2 class="text-[10px] font-bold tracking-wider text-slate-500 uppercase mb-2 flex items-center">
                    <i class="fa-solid fa-users text-slate-400 mr-2"></i> Study Groups
                </h2>
                <ul class="space-y-1">
                    @foreach($groups as $group)
                        <li>
                            <a href="{{ url('/forum-workspace/group/' . $group->id) }}" 
                               class="flex items-center space-x-2 px-2 py-1.5 text-xs rounded-md transition-colors hover:bg-slate-800 hover:text-white 
                               {{ ($type === 'group' && $id == $group->id) ? 'bg-slate-800 text-white font-medium' : '' }}">
                                <i class="fa-regular fa-comments text-emerald-400"></i>
                                <span class="truncate">{{ $group->name }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Main Chat Feed Interface Panel Container -->
    <div class="flex-1 flex flex-col h-full bg-[#f8fafc] relative">
        
        <!-- Message Historical Feed List Stream Container Wrapper -->
        <div id="chat-messages-container" class="flex-1 overflow-y-auto px-8 py-6 space-y-4 pb-24 scroll-smooth">
            @if(count($messages) > 0)
                @foreach($messages as $msg)
                    <div class="flex items-end space-x-3 {{ $msg->user_id === auth()->id() ? 'flex-row-reverse space-x-reverse' : '' }} mb-1">
                        
                        <!-- OWNER INITIALS VALUE: Displays "ME" if you sent it, otherwise peer initials -->
                        <div class="h-9 w-9 rounded-full bg-[#0b1329] text-slate-300 flex items-center justify-center text-xs font-bold shrink-0 shadow">
                            @if($msg->user_id === auth()->id())
                                ME
                            @else
                                {{ strtoupper(substr($msg->user->name ?? 'U', 0, 2)) }}
                            @endif
                        </div>
                        
                        <div class="flex flex-col max-w-xl {{ $msg->user_id === auth()->id() ? 'items-end' : 'items-start' }}">
                            <!-- EYE-SAFE LIGHT BLUE BUBBLES -->
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

        <!-- Sticky Input Tray Box Wrapper Container -->
        <div class="absolute bottom-0 left-0 right-0 p-4 bg-slate-50 border-t border-slate-200 z-10">
            <form id="chat-form" method="POST" action="{{ route('chat.store', ['type' => $type ?? 'broadcast', 'id' => $id ?? 'general']) }}" class="flex items-center space-x-3 max-w-7xl mx-auto">
                @csrf
                
                <!-- Restrict Dropdown Toggle Card Component Module -->
                <div class="relative inline-block text-left">
                    <button type="button" id="dropdown-toggle" class="flex items-center space-x-1.5 px-3 py-2 bg-[#0b1329] border border-slate-950 rounded-xl text-slate-300 shadow-sm focus:outline-none hover:bg-slate-800 transition-all cursor-pointer">
                        <i class="fa-solid fa-shield-halved text-xs text-sky-400"></i>
                        <span class="text-[10px] font-bold tracking-wide uppercase">Restrict</span>
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <i class="fa-solid fa-chevron-up text-[9px] text-slate-400 ml-0.5"></i>
                    </button>

                    <!-- Dynamic Dropdown Options Container Menu Layout Box -->
                    <div id="dropdown-menu" class="hidden absolute bottom-full mb-2 left-0 w-64 bg-white rounded-xl shadow-xl border border-slate-200 overflow-hidden divide-y divide-slate-100 z-50">
                        <div class="py-1">
                            <span class="block px-3 py-1.5 text-[9px] font-bold text-slate-400 uppercase tracking-wider">Filter By Course Code</span>
                            @foreach($topics as $topic)
                                <a href="{{ url('/forum-workspace/topic/' . $topic->id) }}" class="block px-3 py-2 text-xs text-slate-700 hover:bg-slate-50 hover:text-blue-600 truncate {{ ($type === 'topic' && $id == $topic->id) ? 'bg-blue-50 text-blue-600 font-semibold' : '' }}">
                                    <span class="bg-blue-100 text-blue-800 font-mono text-[10px] px-1.5 py-0.5 rounded mr-1.5 border border-blue-200 font-semibold">
                                        {{ $topic->course_code ?? 'CODE' }}
                                    </span>
                                    <span class="text-slate-500 text-[11px]">{{ $topic->title }}</span>
                                </a>
                            @endforeach
                        </div>
                        <div class="py-1">
                            <span class="block px-3 py-1.5 text-[9px] font-bold text-slate-400 uppercase tracking-wider">Active Group Forums</span>
                            @foreach($groups as $group)
                                <a href="{{ url('/forum-workspace/group/' . $group->id) }}" class="block px-3 py-2 text-xs text-slate-700 hover:bg-slate-50 hover:text-emerald-600 truncate {{ ($type === 'group' && $id == $group->id) ? 'bg-emerald-50 text-emerald-600 font-semibold' : '' }}">
                                    <i class="fa-regular fa-comments mr-1.5 text-emerald-400"></i> {{ $group->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex-1 relative flex items-center">
                    <input type="text" id="message-input" name="body" required autocomplete="off" placeholder="Write an answer or update workspace thread..." 
                           class="w-full text-sm px-4 py-2.5 bg-white rounded-xl focus:outline-none focus:ring-1 focus:ring-slate-800 transition-all text-slate-800 placeholder-slate-400 shadow-sm border border-slate-200">
                </div>

                <button type="submit" id="send-btn" class="w-10 h-10 bg-[#0b1329] hover:bg-slate-800 text-slate-200 rounded-full transition-all shadow active:scale-95 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                </button>
            </form>
        </div>
    </div>
</div>

@vite(['resources/js/app.js'])

<script type="module">
    const container = document.getElementById('chat-messages-container');
    const fallbackEmpty = document.getElementById('fallback-empty');
    const currentUserId = {{ auth()->id() }};

    // --- Dropdown Menu System Toggle Controls ---
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

    // DYNAMIC INJECTION ENGINE: Handles message appends locally and from websockets instantly
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

            // Instantly render for the current user
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

    // 🎯 INSTANT WEBSOCKET ENGINE: Listens to the active room channel dynamically
    const currentChannelType = "{{ $type ?? 'broadcast' }}";
    const currentChannelId = "{{ $id ?? 'general' }}";

    if (window.Echo) {
        window.Echo.channel(`chat.${currentChannelType}.${currentChannelId}`)
            .listen('MessageSent', (e) => {
                // Instantly inject the message for the other user without reloading the page
                if (parseInt(e.message.user_id) !== currentUserId) {
                    const body = e.message.body;
                    const senderName = e.message.user ? e.message.user.name : 'Peer User';
                    const senderId = e.message.user_id;
                    
                    appendNewMessage(body, senderName, senderId);
                }
            });
    }
</script>
</body>
</html>