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
        .menu-item a { display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: #94a3b8; text-decoration: none; font-size: 14px; font-weight: 600; }
        .menu-item.active a { color: white; background: #2563eb; border-radius: 8px; margin: 0 10px; }
        .logout-form { margin-top: auto; padding: 20px 0; border-top: 1px solid rgba(255,255,255,0.1); }
        .logout-btn { width: 100%; background: none; border: none; display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: #cbd5e1; font-size: 14px; font-weight: 600; cursor: pointer; }

        /* Main Workspace */
        .main-content { flex-grow: 1; padding: 40px; }
        .welcome-title { font-size: 28px; font-weight: 700; color: #0f172a; }
        .welcome-subtitle { font-size: 15px; color: #475569; margin-top: 6px; margin-bottom: 30px; }

        /* Action Buttons Row */
        .action-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 35px; }
        .action-card { border-radius: 12px; padding: 24px; text-align: center; font-weight: 700; font-size: 16px; color: #0f172a; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 16px; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03); cursor: pointer; text-decoration: none; }
        .action-icon { width: 44px; height: 44px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px; }
        
        .create-topic { background-color: #a7f3d0; } .create-topic .action-icon { background-color: #059669; }
        .create-quiz { background-color: #e9d5ff; } .create-quiz .action-icon { background-color: #7c3aed; }
        .upload-resource { background-color: #bfdbfe; } .upload-resource .action-icon { background-color: #2563eb; }
        .view-students { background-color: #ffedd5; } .view-students .action-icon { background-color: #ea580c; }

        /* Overview Section */
        .overview-card { background: white; border-radius: 12px; padding: 24px; max-width: 420px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03); border: 1px solid #e2e8f0; }
        .overview-title { font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 16px; }
        .stats-subgrid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .stat-mini { border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; text-align: center; }
        .stat-mini-title { font-size: 11px; color: #64748b; font-weight: 600; margin-bottom: 4px; }
        .stat-mini-value { font-size: 24px; font-weight: 700; color: #0f172a; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-brand">Smart Discussion Forum</div>
        <ul class="sidebar-menu">
            <li class="menu-item active"><a href="#">Dashboard</a></li>
            <li class="menu-item"><a href="#">Profile</a></li>
            <li class="menu-item"><a href="#">Manage Discussion</a></li>
            <li class="menu-item"><a href="#">Charts</a></li>
        </ul>
        <div class="logout-form">
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </aside>
    <main class="main-content">
        <h1 class="welcome-title">Welcome, {{ Auth::user()->name }}! 👋</h1>
        <p class="welcome-subtitle">Here's your integrated overview for today.</p>

        <section class="action-grid">
            <div class="action-card create-topic"><div class="action-icon">📝</div>Create Topic</div>
            
            <!-- 👨‍🏫 Renamed Card & Connected to Grades Route -->
            <a href="{{ route('quizzes.grades', ['quizID' => 1]) }}" class="action-card create-quiz">
                <div class="action-icon">📋</div>Quiz Marks
            </a>
            
            <div class="action-card upload-resource"><div class="action-icon">📤</div>Upload Resource</div>
            <div class="action-card view-students"><div class="action-icon">👥</div>View Students</div>
        </section>

        <section class="overview-card">
            <div class="overview-title">Students Overview</div>
            <div class="stats-subgrid">
                <div class="stat-mini"><div class="stat-mini-title">Total Students</div><div class="stat-mini-value">120</div></div>
                <div class="stat-mini" style="color: #16a34a;"><div class="stat-mini-title">Active Students</div><div class="stat-mini-value">95</div></div>
                <div class="stat-mini" style="color: #ea580c;"><div class="stat-mini-title">Inactive Students</div><div class="stat-mini-value">25</div></div>
                <div class="stat-mini" style="color: #dc2626;"><div class="stat-mini-title">Suspended Students</div><div class="stat-mini-value">0</div></div>
            </div>
        </section>

        <div style="background: white; padding: 20px; border-radius: 8px; margin-top: 20px; border: 1px solid #e2e8f0;">
            <h3 style="color: #0b2265; margin-bottom: 10px;">🎯 Quiz Management</h3>
            <p style="color: #64748b; font-size: 14px; margin-bottom: 15px;">Set up new course assessments, manage active timelines, and monitor student progress metrics.</p>
            <a href="{{ route('quizzes.create') }}" style="display: inline-block; padding: 10px 20px; background: #7c3aed; color: white; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 14px;">
                ➕ Setup & Publish New Quiz
            </a>
        </div>

    </main>
</body>
</html>