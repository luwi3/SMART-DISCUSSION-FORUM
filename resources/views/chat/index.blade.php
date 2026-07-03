<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum Workspace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full overflow-hidden flex flex-col">

    <div class="flex-1 flex min-h-0">
        <aside class="w-64 bg-gray-900 text-gray-300 flex flex-col p-4 shadow-xl overflow-y-auto">
            
            <h2 class="text-[10px] font-black uppercase text-gray-500 mb-2 px-2 tracking-wider">Workspaces</h2>
            <div class="space-y-1 mb-6">
                @foreach($groups as $group)
                    <a href="{{ url('/forum-workspace/group/' . $group->id) }}" 
                       class="flex items-center px-3 py-2 rounded-lg text-xs font-medium transition {{ (isset($type) && $type == 'group' && $id == $group->id) ? 'bg-emerald-600 text-white' : 'hover:bg-gray-800' }}">
                        <i class="fa-regular fa-folder-open mr-2"></i> {{ $group->name }}
                    </a>
                @endforeach
            </div>

            <h2 class="text-[10px] font-black uppercase text-gray-500 mb-2 px-2 tracking-wider">🔥 Active Topics</h2>
            <div class="space-y-1 mb-6">
              @foreach($topics as $topic)
    <a href="{{ route('chat.index', ['type' => 'topic', 'id' => $topic->id]) }}" 
       class="flex items-center px-3 py-2 rounded-lg text-xs font-medium text-left hover:bg-gray-700">
        <i class="fa-solid fa-fire text-amber-500 mr-2"></i> 
        {{ $topic->title }}
    </a>
@endforeach
            </div>

            <h2 class="text-[10px] font-black uppercase text-gray-500 mb-2 px-2 tracking-wider">⏱️ Recent Topics</h2>
            <div class="space-y-1">
                <button class="w-full flex items-center px-3 py-2 rounded-lg text-xs font-medium text-left hover:bg-gray-800 transition">
                    <i class="fa-regular fa-clock text-blue-400 mr-2"></i> Git Merge Conflicts Help
                </button>
                <button class="w-full flex items-center px-3 py-2 rounded-lg text-xs font-medium text-left hover:bg-gray-800 transition">
                    <i class="fa-regular fa-clock text-blue-400 mr-2"></i> CSS Flexbox vs Grid
                </button>
            </div>
        </aside>

        <main class="flex-1 bg-white flex flex-col min-h-0">
            
            <div class="flex-1 overflow-y-auto p-6 space-y-4">
                @if(isset($currentStreamTarget) && count($messages) > 0)
                    @foreach($messages as $msg)
                        <div class="flex items-start space-x-3">
                            <div class="h-8 w-8 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] font-bold">
                                {{ strtoupper(substr($msg->user->name ?? 'U', 0, 2)) }}
                            </div>
                            <div class="flex-1">
                                <span class="text-xs font-bold text-gray-900">{{ $msg->user->name }}</span>
                                <div class="mt-1 text-xs text-gray-700 bg-gray-50 p-2 rounded-xl border">{{ $msg->body }}</div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="h-full flex flex-col items-center justify-center text-gray-400 p-8 text-center">
                        <i class="fa-solid fa-comments text-6xl opacity-20 mb-4"></i>
                        <h2 class="text-sm font-bold text-gray-600">Welcome to the Forum Stream</h2>
                        <p class="text-xs mt-2 max-w-xs">Type an open question below, or select a workspace channel to inspect filtered historical archives.</p>
                    </div>
                @endif
            </div>

            <div class="p-4 border-t border-gray-200 bg-gray-50">
                @if($id)
             <form id="chat-form" action="{{ route('chat.store', ['type' => $type, 'id' => $id]) }}" method="POST" class="flex items-center gap-2">
                    @csrf
                    <div class="relative flex items-center">
                        <span class="absolute left-3 text-gray-400 text-xs pointer-events-none">
                            <i class="fa-solid fa-user-shield mr-1"></i> Restrict:
                        </span>
                        <select name="course_id" id="course-filter" 
                                class="pl-20 pr-3 py-2 bg-white border border-gray-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-emerald-500 outline-none cursor-pointer text-gray-700 appearance-none shadow-sm">
                            <option value="all">🌍 All Streams</option>
                            @if(isset($courses))
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">📚 {{ $course->code }}</option>
                                @endforeach
                            @else
                                <option value="BIT2201">📚 BIT 2201</option>
                                <option value="BCS2104">📚 BCS 2104</option>
                            @endif
                        </select>
                    </div>

                    <input type="text" id="message-input" name="body" required autocomplete="off" placeholder="Ask a general question or broadcast a thought..." 
                           class="flex-1 pl-4 py-2 bg-white border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 outline-none shadow-sm">
                    
                    <button type="submit" id="send-btn" class="bg-emerald-600 text-white px-5 py-2 rounded-xl font-bold text-xs hover:bg-emerald-700 transition shadow-sm uppercase tracking-wider">
                        SEND
                    </button>
                </form>
                @else
    <div class="text-center text-gray-500">
        Please select a group or topic from the sidebar to start chatting!
    </div>
@endif
            </div>
        </main>
    </div>

<script type="module">
    // 🧪 Test logs to see what JavaScript detects on load
    console.log("Script block has executed!");
    console.log("Looking for form:", document.getElementById('chat-form'));

    const chatForm = document.getElementById('chat-form');

    if (chatForm) {
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault(); // 🛑 Stops full page reload
            
            console.log('Form submit intercepted successfully!'); 
            
            const input = document.getElementById('message-input');
            const formData = new FormData(this);
            const targetUrl = "{{ isset($type) && isset($id) ? url('/forum-workspace/' . $type . '/' . $id) : url('/messages/broadcast') }}";

            fetch(targetUrl, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).then(() => {
                input.value = ''; 
            });
        });
    }

    @if(isset($type) && isset($id))
        window.Echo.channel('chat.{{ $type }}.{{ $id }}')
            .listen('MessageSent', (e) => {
                console.log('New message received via Echo:', e.message);
            });
    @endif
</script>
</body>
</html>