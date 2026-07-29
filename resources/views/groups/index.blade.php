<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discover Groups - Smart Discussion Forum</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        body {
            background-color: #0f172a;
            color: #f8fafc;
            min-height: 100vh;
        }

        .workspace-layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            background-color: #1e293b;
            padding: 24px 16px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            flex-shrink: 0;
        }

        .sidebar-menu {
            list-style-type: none;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .menu-item {
            padding: 12px 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .menu-item a {
            color: #94a3b8;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .menu-item:hover, .menu-item.active {
            background-color: rgba(255, 255, 255, 0.04);
        }
        
        .menu-item:hover a, .menu-item.active a {
            color: #ffffff;
        }

        .submenu-list {
            list-style-type: none;
            padding-left: 16px;
            margin-top: 8px;
        }

        .submenu-item {
            padding: 6px 0;
        }

        .submenu-item a {
            font-size: 14px;
            font-weight: 500;
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.2s;
        }

        .submenu-item a:hover {
            color: #10b981 !important;
        }

        .logout-btn {
            width: 100%;
            padding: 12px;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s;
        }
        .logout-btn:hover { background: #dc2626; }

        .main-content {
            flex: 1;
            padding: 40px;
            background: #0f172a;
            overflow-y: auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            padding-bottom: 20px;
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #ffffff;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
        }

        .group-card {
            background: #1e293b;
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 14px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .group-name {
            font-size: 18px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 8px;
        }

        .group-desc {
            font-size: 14px;
            color: #94a3b8;
            line-height: 1.5;
            margin-bottom: 16px;
        }

        .member-count {
            font-size: 12px;
            background: rgba(255, 255, 255, 0.06);
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            color: #38bdf8;
        }

        .btn {
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: 0.2s;
            text-align: center;
        }
        .btn-primary { background: #6366f1; color: white; }
        .btn-primary:hover { background: #4f46e5; }
        
        .btn-success { background: #10b981; color: white; }
        .btn-success:hover { background: #059669; }

        .btn-outline-danger { background: transparent; color: #f43f5e; border: 1px solid rgba(244, 63, 94, 0.2); }
        .btn-outline-danger:hover { background: rgba(244, 63, 94, 0.05); }

        .btn-secondary { background: rgba(255, 255, 255, 0.06); color: white; border: 1px solid rgba(255, 255, 255, 0.05); }
        .btn-secondary:hover { background: rgba(255, 255, 255, 0.1); }

        .roster-panel {
            margin-top: 16px;
            padding-top: 14px;
            border-top: 1px dashed rgba(255, 255, 255, 0.08);
        }
        .roster-title {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 8px;
            display: block;
        }
        .roster-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            color: #94a3b8;
            padding: 4px 0;
        }
        .kick-btn {
            background: none;
            border: none;
            color: #f43f5e;
            cursor: pointer;
            font-weight: 700;
            font-size: 12px;
        }
        .kick-btn:hover { text-decoration: underline; }

        .mobile-menu-toggle { display: none; background: none; border: none; font-size: 20px; cursor: pointer; padding: 4px; color: #f8fafc; margin-bottom: 16px; }
        .sidebar-backdrop { display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.5); z-index: 900; }
        .sidebar-backdrop.active { display: block; }

        @media (max-width: 768px) {
            .mobile-menu-toggle { display: inline-flex; align-items: center; }
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                z-index: 950;
                transform: translateX(-100%);
                transition: transform 0.25s ease;
                box-shadow: 6px 0 24px rgba(0,0,0,0.4);
            }
            .sidebar.mobile-open { transform: translateX(0); }
            .main-content { padding: 20px; }
            .page-header { flex-direction: column; align-items: flex-start; gap: 12px; }
        }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body>

    <div class="workspace-layout">
        <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

        <aside class="sidebar" id="sidebar">
            <ul class="sidebar-menu">
                <li class="menu-item"><a href="{{ route('student.dashboard') }}">Main Menu</a></li>
                <li class="menu-item"><a href="#">Profile</a></li>
                <li class="menu-item"><a href="#">Marks</a></li>
                <li class="menu-item"><a href="{{ route('chat.index') }}">Chats</a></li>
                <li class="menu-item"><a href="#">Notifications</a></li>
                <li class="menu-item"><a href="#">Announcements</a></li>
                
                <li class="menu-item active" x-data="{ open: true }" style="height: auto;">
                    <div @click="open = !open" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                        <a href="{{ route('groups.index') }}" @click.stop>Groups</a>
                        <svg :class="{ 'rotate-180': open }" style="width: 14px; height: 14px; fill: currentColor; color: #64748b; transition: transform 0.2s;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>

                    <ul x-show="open" x-transition class="submenu-list">
                        @if(isset($sidebarGroups) && count($sidebarGroups) > 0)
                            @foreach($sidebarGroups as $sGroup)
                                <li class="submenu-item">
                                    <a href="{{ route('chat.index', ['type'=>'group','id'=>$sGroup->id]) }}">
                                
                                        # {{ $sGroup->name }}
                                    </a>
                                </li>
                            @endforeach
                        @else
                            <li class="submenu-item" style="color: #64748b; font-style: italic; font-size: 13px;">No joined groups</li>
                        @endif
                    </ul>
                </li>
            </ul>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </aside>

        <main class="main-content">
            <button type="button" class="mobile-menu-toggle" id="mobile-menu-toggle" aria-label="Open menu">☰ Menu</button>

            <div class="page-header">
                <div>
                    <h1 class="page-title">🌐 Discover Study Groups</h1>
                    <p style="color: #64748b; margin-top: 4px; font-size: 14px;">Explore public team channels or administer owned systems.</p>
                </div>
                <a href="{{ route('groups.create') }}" class="btn btn-success">+ Create Group</a>
            </div>

            @if(session('success'))
                <div style="background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); padding: 14px; border-radius: 8px; margin-bottom: 24px; font-size: 14px; font-weight: 600;">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <div class="grid-container">
                @foreach($groups as $group)
                    @php 
                        $isCreator = ($group->user_id === auth()->id());
                        $isMember = $group->members->contains(auth()->id()); 
                    @endphp

                    <div class="group-card">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                                <h3 class="group-name"># {{ $group->name }}</h3>
                                <span class="member-count">👥 {{ $group->members_count }}</span>
                            </div>
                            <p class="group-desc">{{ $group->description }}</p>

                            @if($isCreator && count($group->members) > 1)
                                <div class="roster-panel">
                                    <span class="roster-title">Active Group Roster:</span>
                                    @foreach($group->members as $member)
                                        @if($member->id !== auth()->id())
                                            <div class="roster-item">
                                                <span>👤 {{ $member->name }}</span>
                                                <form action="{{ route('groups.remove_user', [$group->id, $member->id]) }}" method="POST" onsubmit="return confirm('Remove this student from the discussion workspace?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="kick-btn">Kick</button>
                                                </form>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div style="display: flex; gap: 12px; margin-top: 24px;">
                            @if($isCreator)
                               <a href="{{ route('chat.index', ['type'=>'group','id'=>$group->id]) }}" class="btn btn-secondary" style="flex: 1;">Enter Chat</a>
                                <form action="{{ route('groups.destroy', $group->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this group permanently?');" style="flex: 1;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" style="width: 100%;">Delete Group</button>
                                </form>
                            @else
                                @if($isMember)
                                    <a href="{{ route('chat.index', ['type'=>'group','id'=>$group->id]) }}" 
   class="btn btn-secondary" style="flex: 1;">
    Enter Chat
</a>
                                @endif
                                <form action="{{ route('groups.join', $group->id) }}" method="POST" style="flex: 1;">
                                    @csrf
                                    <button type="submit" class="btn {{ $isMember ? 'btn-outline-danger' : 'btn-primary' }}" style="width: 100%;">
                                        {{ $isMember ? 'Leave Group' : 'Join Group' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </main>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('mobile-menu-toggle');
        const sidebarEl = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');

        function openSidebar() {
            if (sidebarEl) sidebarEl.classList.add('mobile-open');
            if (backdrop) backdrop.classList.add('active');
        }
        function closeSidebar() {
            if (sidebarEl) sidebarEl.classList.remove('mobile-open');
            if (backdrop) backdrop.classList.remove('active');
        }

        if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
        if (backdrop) backdrop.addEventListener('click', closeSidebar);
    });
</script>
</body>
</html>