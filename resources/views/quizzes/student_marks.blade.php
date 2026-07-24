<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Marks - Smart Discussion Forum</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/js/app.js'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background-color: #f8fafc; min-height: 100vh; display: flex; flex-direction: column; }

        .top-brand-bar {
            background-color: #0c2340;
            color: white;
            padding: 14px 30px;
            font-weight: 700;
            font-size: 16px;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .workspace-layout { display: flex; flex-grow: 1; }

        /* Sidebar Styling (Matches Dashboard) */
        .sidebar { width: 260px; background-color: #0a1931; color: white; display: flex; flex-direction: column; flex-shrink: 0; }
        .sidebar-menu { list-style: none; padding: 24px 0; }

        .menu-item a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 24px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: background-color 0.15s ease, color 0.15s ease;
        }
        .menu-item.active a { color: white; background: #2563eb; }
        .menu-item a:hover { color: white !important; background-color: #2563eb; }

        .badge { background: #ef4444; color: white; font-size: 11px; padding: 2px 7px; border-radius: 20px; font-weight: 700; }
        .logout-btn { width: 100%; background: none; border: none; padding: 14px 24px; color: #f43f5e; text-align: left; font-weight: 700; font-size: 14px; cursor: pointer; margin-top: auto; border-top: 1px solid rgba(255,255,255,0.05); transition: background 0.2s; }
        .logout-btn:hover { background: rgba(244, 63, 94, 0.1); }

        /* Main Marks Content Area */
        .main-content { flex-grow: 1; padding: 40px; display: flex; flex-direction: column; gap: 30px; }
        .content-panel { background: white; border-radius: 12px; padding: 30px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.02); max-width: 1100px; }

        /* Table Layout */
        .list-row-item { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 12px; }
        .list-row-item:last-child { margin-bottom: 0; }
        .item-info-meta { display: flex; flex-direction: column; gap: 4px; }
        .item-info-title { font-size: 15px; font-weight: 700; color: #1e293b; }
        .item-info-badge { font-size: 12px; color: #64748b; background: #e2e8f0; padding: 2px 8px; border-radius: 4px; font-weight: 600; width: fit-content; }

        .grade-display { text-align: right; }
        .grade-score { font-size: 18px; font-weight: 800; color: #2563eb; }
        .grade-total { font-size: 13px; color: #64748b; font-weight: 500; }
        .grade-time { font-size: 12px; color: #94a3b8; margin-top: 4px; display: block; }
    </style>
</head>
<body>
    @php
        try {
            $initialUnread = auth()->user()->unreadNotifications()->count();
        } catch (\Exception $e) {
            $initialUnread = 0;
        }
    @endphp

    <div class="top-brand-bar">
        <span>SMART DISCUSSION FORUM</span>

        <div id="notification-dropdown" style="position: relative; display: inline-block;">
            <a href="{{ route('student.dashboard', ['tab' => 'notifications']) }}" style="background: none; border: none; cursor: pointer; padding: 4px; display: flex; align-items: center; position: relative;">
                <svg style="width: 22px; height: 22px; stroke: #94a3b8; fill: none; transition: stroke 0.2s;" onmouseover="this.style.stroke='#ffffff'" onmouseout="this.style.stroke='#94a3b8'" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <span id="notification-count" style="position: absolute; top: -2px; right: -2px; background: #ef4444; color: white; border-radius: 9999px; padding: 2px 6px; font-size: 9px; font-weight: bold; line-height: 1; display: {{ $initialUnread > 0 ? 'inline-block' : 'none' }};">
                    {{ $initialUnread }}
                </span>
            </a>
        </div>
    </div>

    <div class="workspace-layout">

        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li class="menu-item {{ request()->routeIs('student.dashboard') && (!request('tab') || request('tab') === 'main') ? 'active' : '' }}">
                    <a href="{{ route('student.dashboard', ['tab' => 'main']) }}">Main Menu</a>
                </li>
                <li class="menu-item {{ request()->routeIs('student.dashboard') && request('tab') === 'profile' ? 'active' : '' }}">
                    <a href="{{ route('student.dashboard', ['tab' => 'profile']) }}">Profile</a>
                </li>
                <li class="menu-item {{ request()->routeIs('student.marks') ? 'active' : '' }}">
                    <a href="{{ route('student.marks') }}">Marks</a>
                </li>
                <li class="menu-item"><a href="{{ route('chat.index') }}">Chats</a></li>
                <li class="menu-item {{ request()->routeIs('student.dashboard') && request('tab') === 'notifications' ? 'active' : '' }}">
                    <a href="{{ route('student.dashboard', ['tab' => 'notifications']) }}">
                        Notifications
                        <span class="badge" id="sidebar-badge-counter" style="display: {{ $initialUnread > 0 ? 'inline-block' : 'none' }};">{{ $initialUnread }}</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('student.dashboard') && request('tab') === 'announcements' ? 'active' : '' }}">
                    <a href="{{ route('student.dashboard', ['tab' => 'announcements']) }}">Announcements</a>
                </li>

                <li class="menu-item" x-data="{ open: false }" style="display: block; height: auto; padding-bottom: 0;">
                    <div @click="open = !open" style="display: flex; justify-content: space-between; align-items: center; width: 100%; cursor: pointer; padding: 14px 24px;">
                        <a href="#" @click.prevent style="flex-grow: 1; display: inline-block; color: #94a3b8; text-decoration: none; font-weight: 600; font-size: 14px;">Groups</a>
                        <svg :class="{ 'rotate-180': open }" class="transform transition-transform duration-150" style="width: 14px; height: 14px; fill: currentColor; transition: transform 0.2s; margin-right: 20px; color: #94a3b8;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>

                    <ul x-show="open" x-transition class="nested-groups-drawer" style="list-style-type: none; padding-left: 24px; padding-bottom: 12px; margin-top: 4px; display: none;">
                        @if(isset($sidebarGroups) && $sidebarGroups->count() > 0)
                            @foreach($sidebarGroups as $group)
                                <li style="padding: 6px 0;">
                                    <a href="{{ route('chat.index', ['type' => 'group', 'id' => $group->id]) }}" style="color: #a0aec0; text-decoration: none; font-size: 0.9em; transition: color 0.2s;" onmouseover="this.style.color='#10b981'" onmouseout="this.style.color='#a0aec0'">
                                        # {{ $group->name }}
                                    </a>
                                </li>
                            @endforeach
                        @else
                            <li style="padding: 6px 0; font-size: 0.85em; color: #718096; font-style: italic;">No groups found</li>
                        @endif

                        <li style="padding: 8px 0 0 0; margin-top: 4px; border-top: 1px solid rgba(255,255,255,0.08);">
                            <a href="{{ route('groups.create') }}" style="color: #10b981; font-weight: bold; font-size: 0.85em; text-decoration: none; transition: opacity 0.2s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                + New Group
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </aside>

        <main class="main-content">
            <section class="content-panel">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px; border-bottom: 2px solid #f1f5f9; padding-bottom: 16px;">
                    <span style="font-size: 24px;">🏆</span>
                    <div>
                        <h2 style="font-size: 20px; font-weight: 700; color: #0f172a;">My Quiz Performance Ledger</h2>
                        <p style="font-size: 14px; color: #64748b;">Track your official assessment results and score summaries</p>
                    </div>
                </div>

                @if($grades->isEmpty())
                    <div style="text-align: center; padding: 50px 20px; color: #64748b; background: #f8fafc; border-radius: 8px; border: 2px dashed #cbd5e1;">
                        <p style="font-size: 15px; font-weight: 600; color: #334155;">No evaluation logs found</p>
                        <p style="font-size: 13px; color: #94a3b8; margin-top: 4px;">Once you complete an active course quiz, your grading records will show up here.</p>
                    </div>
                @else
                    @foreach($grades as $grade)
                        <div class="list-row-item">
                            <div class="item-info-meta">
                                <span class="item-info-title">{{ $grade->quiz_title }}</span>
                                <span class="item-info-badge">{{ $grade->courseCode }}</span>
                            </div>
                            <div class="grade-display">
                                <span class="grade-score">{{ $grade->score }}</span>
                                <span class="grade-total">/ {{ $grade->total_questions }}</span>
                                <span class="grade-time">
                                    {{ \Carbon\Carbon::parse($grade->timeSubmitted)->format('M d, Y @ h:i A') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                @endif
            </section>
        </main>

    </div>
</body>
</html>