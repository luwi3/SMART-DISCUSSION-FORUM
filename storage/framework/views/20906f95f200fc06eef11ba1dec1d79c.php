<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Smart Forum Workspace</title>
    
    <!-- Tailwind CSS CDN -->
   <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="bg-gray-100 antialiased font-sans">

<div class="flex h-screen bg-white">
    <!-- Sidebar Workspace Navigation Container -->
    <div class="w-64 bg-[#0b1329] text-slate-300 flex flex-col border-r border-slate-900">
        <div class="p-4 border-b border-slate-900 flex items-center justify-between">
            <h1 class="text-xs font-bold tracking-wider text-slate-400 uppercase">Workspaces</h1>
            <span class="text-[11px] bg-slate-800 text-slate-300 px-2 py-0.5 rounded-full border border-slate-700 font-mono">
                <?php echo e(auth()->user()->username ?? auth()->user()->name); ?>

            </span>
        </div>
        
        <div class="flex-1 overflow-y-auto p-4 space-y-6">
            <!-- Active Courses Sidebar Collection Container -->
<div>
    <h2 class="text-[10px] font-bold tracking-wider text-slate-500 uppercase mb-2 flex items-center">
        <i class="fa-solid fa-graduation-cap text-sky-400 mr-2"></i> Active Courses
    </h2>
    <ul class="space-y-1">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $topics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <li>
                <a href="<?php echo e(url('/forum-workspace/topic/' . $topic->id)); ?>" 
                   class="flex items-center justify-between px-2 py-1.5 text-xs rounded-md transition-colors hover:bg-slate-800 hover:text-white 
                   <?php echo e(($type === 'topic' && $id == $topic->id) ? 'bg-slate-800 text-white font-medium' : ''); ?>">
                    
                    <!-- Left Side: Icon and Title (Keeps your exact logic) -->
                    <div class="flex items-center space-x-2 truncate mr-2">
                        <i class="fa-solid fa-book-bookmark text-blue-400 shrink-0"></i>
                        <span class="truncate"><?php echo e($topic->course_code ?? $topic->title); ?></span>
                    </div>

                    <!-- Right Side: Download Icon Link (Isolated Feature) -->
                    <object>
                        <a href="<?php echo e(route('topics.export-pdf', $topic->id)); ?>" 
                           title="Export to PDF" 
                           class="text-slate-400 hover:text-sky-400 transition-colors p-0.5 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                        </a>
                    </object>
                </a>
            </li>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </ul>
</div>

            <!-- Recent Group Discussions Sidebar Collection Container -->
            <div>
                <h2 class="text-[10px] font-bold tracking-wider text-slate-500 uppercase mb-2 flex items-center">
                    <i class="fa-solid fa-users text-slate-400 mr-2"></i> Study Groups
                </h2>
                <ul class="space-y-1">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <li>
                            <a href="<?php echo e(url('/forum-workspace/group/' . $group->id)); ?>" 
                               class="flex items-center space-x-2 px-2 py-1.5 text-xs rounded-md transition-colors hover:bg-slate-800 hover:text-white 
                               <?php echo e(($type === 'group' && $id == $group->id) ? 'bg-slate-800 text-white font-medium' : ''); ?>">
                                <i class="fa-regular fa-comments text-emerald-400"></i>
                                <span class="truncate"><?php echo e($group->name); ?></span>
                            </a>
                        </li>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Main Chat Feed Interface Panel Container -->
    <div class="flex-1 flex flex-col h-full bg-[#f8fafc] relative">
        
        <!-- Message Historical Feed List Stream Container Wrapper -->
        <div id="chat-messages-container" class="flex-1 overflow-y-auto px-8 py-6 space-y-4 pb-24 scroll-smooth">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($messages) > 0): ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="flex items-end space-x-3 <?php echo e($msg->user_id === auth()->id() ? 'flex-row-reverse space-x-reverse' : ''); ?> mb-1">
                        
                        <!-- OWNER INITIALS VALUE: Displays "ME" if you sent it, otherwise peer initials -->
                        <div class="h-9 w-9 rounded-full bg-[#0b1329] text-slate-300 flex items-center justify-center text-xs font-bold shrink-0 shadow">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($msg->user_id === auth()->id()): ?>
                                ME
                            <?php else: ?>
                                <?php echo e(strtoupper(substr($msg->user->name ?? 'U', 0, 2))); ?>

                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        
                        <div class="flex flex-col max-w-xl <?php echo e($msg->user_id === auth()->id() ? 'items-end' : 'items-start'); ?>">
                            <!-- EYE-SAFE LIGHT BLUE BUBBLES -->
                            <div class="relative px-4 py-2.5 shadow-md rounded-xl break-words w-full border
                                <?php echo e($msg->user_id === auth()->id() 
                                    ? 'bg-sky-100 text-slate-900 border-sky-200 rounded-tr-none' 
                                    : 'bg-white text-slate-800 border-slate-100 rounded-tl-none'); ?>">
                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($msg->user_id !== auth()->id()): ?>
                                    <div class="text-[11px] font-bold text-emerald-600 mb-1 tracking-wide uppercase">
                                        <?php echo e($msg->user->name); ?>

                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <div class="pr-14 text-sm font-normal leading-relaxed">
                                    <?php echo e($msg->body); ?>

                                </div>

                                <span class="absolute bottom-1 right-2.5 text-[10px] select-none font-mono
                                    <?php echo e($msg->user_id === auth()->id() ? 'text-slate-500' : 'text-gray-400'); ?>">
                                    <?php echo e($msg->created_at ? $msg->created_at->format('H:i') : now()->format('H:i')); ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($msg->user_id === auth()->id()): ?>
                                        <i class="fa-solid fa-check-double text-sky-600 ml-0.5"></i>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </span>
                            </div>
                        </div>

                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <?php else: ?>
                <div id="fallback-empty" class="h-full flex flex-col items-center justify-center text-slate-400 p-8 text-center">
                    <div class="w-16 h-16 rounded-full bg-[#0b1329] flex items-center justify-center shadow mb-4">
                        <i class="fa-solid fa-graduation-cap text-slate-300 text-2xl"></i>
                    </div>
                    <h2 class="text-sm font-bold text-slate-700">Course Discussion Hub</h2>
                    <p class="text-xs mt-1 max-w-xs text-slate-500">Select a course code thread stream from the menu to open historical records.</p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <!-- Sticky Input Tray Box Wrapper Container -->
        <div class="absolute bottom-0 left-0 right-0 p-4 bg-slate-50 border-t border-slate-200 z-10">
            <form id="chat-form" method="POST" action="<?php echo e(route('chat.store', ['type' => $type ?? 'broadcast', 'id' => $id ?? 'general'])); ?>" class="flex items-center space-x-3 max-w-7xl mx-auto">
                <?php echo csrf_field(); ?>
                
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
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $topics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <a href="<?php echo e(url('/forum-workspace/topic/' . $topic->id)); ?>" class="block px-3 py-2 text-xs text-slate-700 hover:bg-slate-50 hover:text-blue-600 truncate <?php echo e(($type === 'topic' && $id == $topic->id) ? 'bg-blue-50 text-blue-600 font-semibold' : ''); ?>">
                                    <span class="bg-blue-100 text-blue-800 font-mono text-[10px] px-1.5 py-0.5 rounded mr-1.5 border border-blue-200 font-semibold">
                                        <?php echo e($topic->course_code ?? 'CODE'); ?>

                                    </span>
                                    <span class="text-slate-500 text-[11px]"><?php echo e($topic->title); ?></span>
                                </a>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                        <div class="py-1">
                            <span class="block px-3 py-1.5 text-[9px] font-bold text-slate-400 uppercase tracking-wider">Active Group Forums</span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <a href="<?php echo e(url('/forum-workspace/group/' . $group->id)); ?>" class="block px-3 py-2 text-xs text-slate-700 hover:bg-slate-50 hover:text-emerald-600 truncate <?php echo e(($type === 'group' && $id == $group->id) ? 'bg-emerald-50 text-emerald-600 font-semibold' : ''); ?>">
                                    <i class="fa-regular fa-comments mr-1.5 text-emerald-400"></i> <?php echo e($group->name); ?>

                                </a>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
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

<?php echo app('Illuminate\Foundation\Vite')(['resources/js/app.js']); ?>

<script type="module">
    const container = document.getElementById('chat-messages-container');
    const fallbackEmpty = document.getElementById('fallback-empty');
    const currentUserId = <?php echo e(auth()->id()); ?>;

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
            appendNewMessage(messageText, "<?php echo e(auth()->user()->name); ?>", currentUserId);
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
    const currentChannelType = "<?php echo e($type ?? 'broadcast'); ?>";
    const currentChannelId = "<?php echo e($id ?? 'general'); ?>";

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
</html><?php /**PATH C:\Users\DELL\OneDrive\Desktop\Herd\Smart-Discussion-App\resources\views/chat/index.blade.php ENDPATH**/ ?>