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
        <aside class="w-64 bg-gray-900 text-gray-300 flex flex-col p-4 shadow-xl">
            <h2 class="text-[10px] font-black uppercase text-gray-500 mb-4 px-2">Workspaces</h2>
            <div class="space-y-1">
                @foreach($groups as $group)
                    <a href="{{ url('/forum-workspace/group/' . $group->id) }}" 
                       class="flex items-center px-3 py-2 rounded-lg text-xs font-medium transition {{ (isset($type) && $type == 'group' && $id == $group->id) ? 'bg-emerald-600 text-white' : 'hover:bg-gray-800' }}">
                        <i class="fa-regular fa-folder-open mr-2"></i> {{ $group->name }}
                    </a>
                @endforeach
            </div>
        </aside>

        <main class="flex-1 bg-white flex flex-col min-h-0">
            @if(isset($currentStreamTarget))
                <div id="chat-window" class="flex-1 overflow-y-auto p-6 space-y-4">
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
                </div>

                <div class="p-4 border-t border-gray-200 bg-white">
                    <form id="chat-form" class="flex items-center">
                        @csrf
                        <input type="text" id="message-input" name="body" required autocomplete="off" placeholder="Type a message..." 
                               class="w-full pl-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 outline-none">
                        <button type="submit" id="send-btn" class="ml-2 bg-emerald-600 text-white px-4 py-2 rounded-lg font-bold text-[10px] hover:bg-emerald-700 transition">SEND</button>
                    </form>
                </div>
            @else
                <div class="flex-1 flex flex-col items-center justify-center text-gray-400 p-8">
                    <i class="fa-solid fa-comments text-6xl opacity-20 mb-4"></i>
                    <h2 class="text-sm font-bold text-gray-600">Select a Workspace</h2>
                    <p class="text-xs mt-2">Pick a directory to begin collaborating.</p>
                </div>
            @endif
        </main>
    </div>

    <script type="module">
        @if(isset($type) && isset($id))
            // 1. Listen for incoming messages
            window.Echo.channel('chat.{{ $type }}.{{ $id }}')
                .listen('MessageSent', (e) => {
                    const chatWindow = document.getElementById('chat-window');
                    chatWindow.insertAdjacentHTML('beforeend', `
                        <div class="flex items-start space-x-3 mt-4">
                            <div class="h-8 w-8 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] font-bold">
                                ${e.message.user.name.substring(0, 2).toUpperCase()}
                            </div>
                            <div class="flex-1">
                                <span class="text-xs font-bold text-gray-900">${e.message.user.name}</span>
                                <div class="mt-1 text-xs text-gray-700 bg-gray-50 p-2 rounded-xl border">${e.message.body}</div>
                            </div>
                        </div>`);
                    chatWindow.scrollTop = chatWindow.scrollHeight;
                });

            // 2. Prevent form reload and submit via AJAX
            document.getElementById('chat-form').addEventListener('submit', function(e) {
                e.preventDefault();
                const input = document.getElementById('message-input');
                const formData = new FormData(this);

                fetch("{{ url('/forum-workspace/' . $type . '/' . $id) }}", {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).then(() => {
                    input.value = ''; // Clear input only after successful request
                });
            });
        @endif
    </script>
</body>
</html>