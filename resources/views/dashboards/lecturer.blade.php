<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Dashboard - Smart Discussion Forum</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background-color: #f4f7fc; min-height: 100vh; display: flex; }
        
        /* Sidebar styling from mockup */
        .sidebar { width: 260px; background-color: #0b2265; color: white; display: flex; flex-direction: column; flex-shrink: 0; }
        .sidebar-brand { padding: 24px 20px; font-size: 16px; font-weight: 700; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-menu { list-style: none; padding: 20px 0; flex-grow: 1; display: flex; flex-direction: column; gap: 4px; }
        
        /* Smooth hover-highlight movement setup */
        .menu-item a { 
            display: flex; 
            align-items: center; 
            gap: 12px; 
            padding: 12px 20px; 
            color: #94a3b8; 
            text-decoration: none; 
            font-size: 14px; 
            font-weight: 600; 
            border-radius: 8px; /* Uniform radius on all items prevents layout jumps */
            margin: 0 10px;     /* Pre-applied margin matches your mockup */
            transition: background-color 0.2s ease, color 0.2s ease; /* Glides smoothly */
        }
        
        /* Highlight turns blue ONLY on active state OR when cursor hovers over the button */
        .menu-item.active a,
        .menu-item a:hover { 
            color: white !important; 
            background: #2563eb !important; 
        }
        
        .logout-form { margin-top: auto; padding: 20px 0; border-top: 1px solid rgba(255,255,255,0.1); }
        .logout-btn { width: 100%; background: none; border: none; display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: #cbd5e1; font-size: 14px; font-weight: 600; cursor: pointer; text-align: left; }

        /* Main Workspace */
        .main-content { flex-grow: 1; padding: 40px; display: flex; flex-direction: column; gap: 24px; }
        .welcome-title { font-size: 28px; font-weight: 700; color: #0f172a; }
        .welcome-subtitle { font-size: 15px; color: #475569; margin-top: 6px; margin-bottom: 6px; }

        /* Alert System Box */
        .alert-banner { padding: 16px; background-color: #d1fae5; border: 1px solid #10b981; color: #065f46; border-radius: 8px; font-size: 14px; font-weight: 600; margin-bottom: 10px; }

        /* Action Buttons Row */
        .action-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 10px; }
        .action-card { border-radius: 12px; padding: 24px; text-align: center; font-weight: 700; font-size: 16px; color: #0f172a; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 16px; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03); cursor: pointer; text-decoration: none; transition: transform 0.15s ease; }
        .action-card:hover { transform: translateY(-2px); }
        .action-icon { width: 44px; height: 44px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px; }
        
        .create-topic { background-color: #a7f3d0; } .create-topic .action-icon { background-color: #059669; }
        .create-quiz { background-color: #e9d5ff; } .create-quiz .action-icon { background-color: #7c3aed; }
        .upload-resource { background-color: #bfdbfe; } .upload-resource .action-icon { background-color: #2563eb; }
        .view-students { background-color: #ffedd5; } .view-students .action-icon { background-color: #ea580c; }

        /* Full-width panel group layout */
        .dashboard-split { display: flex; gap: 24px; align-items: flex-start; }
        .left-panel-group { flex: 1; display: flex; flex-direction: column; gap: 24px; }

        /* Panel Styling */
        .content-panel { background: white; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03); }
        .panel-heading { color: #0b2265; margin-bottom: 6px; font-size: 18px; font-weight: 700; }
        .panel-desc { color: #64748b; font-size: 14px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-brand">Smart Discussion Forum</div>
        <ul class="sidebar-menu">
            <li class="menu-item {{ request()->is('dashboard') || request()->is('lecturer/dashboard') || request()->is('/') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}">Dashboard</a>
            </li>
            
            <li class="menu-item {{ request()->is('profile*') ? 'active' : '' }}">
                <a href="{{ route('profile.edit') }}">Profile</a>
            </li>
            
            <li class="menu-item {{ request()->is('chat*') || request()->is('discussion*') ? 'active' : '' }}">
                <a href="{{ route('chat.index') }}">Manage Discussion</a>
            </li>
            
            <li class="menu-item {{ request()->is('participation*') ? 'active' : '' }}">
                <a href="{{ route('participation.index') }}">Participation Marks</a>
            </li>
            
            <li class="menu-item {{ request()->is('resources*') ? 'active' : '' }}">
                <a href="{{ route('resources.index') }}">Upload Resources</a>
            </li>
        </ul>
        
        <div class="logout-form">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </aside>

    <main class="main-content">
        @if(session('success'))
            <div class="alert-banner">
                ✓ {{ session('success') }}
            </div>
        @endif

        <div>
            <h1 class="welcome-title">Welcome, {{ Auth::user()->name }}! 👋</h1>
            <p class="welcome-subtitle">Here's your integrated overview for today.</p>
        </div>

        <section class="action-grid">
            <a href="{{ route('topics.create') }}" class="action-card create-topic">
                <div class="action-icon">📝</div>Create Topic
            </a>
            
            <a href="{{ route('lecturer.quizzes.index') }}" class="action-card create-quiz">
                <div class="action-icon">📋</div>Quiz Marks
            </a>
            
            <a href="{{ route('resources.index') }}" class="action-card upload-resource">
                <div class="action-icon">📤</div>Upload Resource
            </a>
            
            <a href="{{ route('participation.index') }}" class="action-card view-students">
                <div class="action-icon">👥</div>Participation Marks
            </a>
        </section>

        <div class="dashboard-split">
            <div class="left-panel-group">
                <section class="content-panel">
                    <h3 class="panel-heading">🎯 Quiz Creation</h3>
                    <p class="panel-desc">Set up new course assessments, manage active timelines, and monitor student progress metrics.</p>
                    <a href="{{ route('quizzes.create') }}" style="display: inline-block; padding: 10px 20px; background: #7c3aed; color: white; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 14px;">
                        ➕ Setup & Publish New Quiz
                    </a>
                </section>
            </div>
        </div>
    </main>
</body>
</html>