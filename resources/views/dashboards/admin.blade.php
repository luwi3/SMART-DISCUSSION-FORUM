<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Smart Discussion Forum</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background-color: #f0f4f9; min-height: 100vh; display: flex; }
        .sidebar { width: 260px; background-color: #032b88; color: white; display: flex; flex-direction: column; flex-shrink: 0; }
        .sidebar-brand { padding: 30px 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .brand-icon { width: 44px; height: 44px; color: white; margin-bottom: 10px; }
        .brand-text { font-size: 14px; font-weight: 900; letter-spacing: 0.5px; }
        .sidebar-menu { list-style: none; padding: 20px 0; flex-grow: 1; display: flex; flex-direction: column; gap: 4px; }
        .menu-item a { display: flex; align-items: center; gap: 14px; padding: 12px 24px; color: rgba(255,255,255,0.7); text-decoration: none; font-size: 14px; font-weight: 600; }
        .menu-item.active a { color: white; background: #2563eb; font-weight: 700; border-radius: 0 50px 50px 0; margin-right: 14px; }
        .logout-form { border-top: 1px solid rgba(255,255,255,0.1); padding: 15px 0; }
        .logout-btn { width: 100%; background: none; border: none; display: flex; align-items: center; gap: 14px; padding: 12px 24px; color: #f43f5e; font-size: 14px; font-weight: 700; cursor: pointer; }
        .main-content { flex-grow: 1; display: flex; flex-direction: column; min-width: 0; }
        .top-navbar { background: white; padding: 18px 30px; display: flex; align-items: center; border-bottom: 1px solid #e2e8f0; }
        .nav-title { font-size: 15px; font-weight: 800; color: #1e293b; }
        .dashboard-body { padding: 30px; max-width: 1300px; width: 100%; margin: 0 auto; }
        .welcome-section { margin-bottom: 24px; }
        .welcome-title { font-size: 24px; font-weight: 700; color: #0f172a; }
        .metrics-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 24px; }
        .metric-card { background: white; border-radius: 12px; padding: 20px; border: 1px solid #eef2f6; }
        .metric-title { font-size: 13px; font-weight: 700; margin-bottom: 16px; }
        .metric-content { display: flex; align-items: center; gap: 14px; margin-bottom: 14px; }
        .metric-icon-box { width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .metric-value { font-size: 28px; font-weight: 800; color: #0f172a; }
        .progress-container { width: 100%; height: 5px; background: #e2e8f0; border-radius: 10px; overflow: hidden; }
        .progress-bar { height: 100%; border-radius: 10px; }
        .metric-percentage { font-size: 11px; color: #64748b; margin-top: 6px; display: block; }
        .students .metric-title { color: #16a34a; } .students .metric-icon-box { background: #f0fdf4; color: #16a34a; } .students .progress-bar { background: #16a34a; width: 80%; }
        .warnings .metric-title { color: #ea580c; } .warnings .metric-icon-box { background: #fff7ed; color: #ea580c; } .warnings .progress-bar { background: #ea580c; width: 12%; }
        .blacklist .metric-title { color: #dc2626; } .blacklist .metric-icon-box { background: #fef2f2; color: #dc2626; } .blacklist .progress-bar { background: #dc2626; width: 5%; }
        .totals .metric-title { color: #2563eb; } .totals .metric-icon-box { background: #eff6ff; color: #2563eb; } .totals .progress-bar { background: #2563eb; width: 100%; }
        .chart-card { background: white; border-radius: 12px; padding: 24px; border: 1px solid #eef2f6; max-width: 600px; }
        .chart-title { font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 24px; }
        .chart-layout { display: flex; align-items: center; gap: 40px; }
        .donut-container { position: relative; width: 150px; height: 150px; }
        .donut-chart { width: 100%; height: 100%; border-radius: 50%; background: conic-gradient(#16a34a 0% 80%, #ea580c 80% 92%, #dc2626 92% 100%); display: flex; align-items: center; justify-content: center; }
        .donut-hole { width: 100px; height: 100px; background: white; border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; }
        .donut-total { font-size: 22px; font-weight: 800; color: #0f172a; }
        .donut-label { font-size: 10px; color: #64748b; font-weight: 600; text-transform: uppercase; }
        .chart-legend { display: flex; flex-direction: column; gap: 14px; flex-grow: 1; }
        .legend-item { display: flex; align-items: center; justify-content: space-between; font-size: 13px; color: #334155; font-weight: 600; }
        .legend-label-group { display: flex; align-items: center; gap: 10px; }
        .legend-dot { width: 12px; height: 12px; border-radius: 3px; }
        .dot-green { background: #16a34a; } .dot-orange { background: #ea580c; } .dot-red { background: #dc2626; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-brand">
            <svg class="brand-icon" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>
            <div class="brand-text">SMART DISCUSSION FORUM</div>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-item active"><a href="#">Dashboard</a></li>
            <li class="menu-item"><a href="#">Profile</a></li>
            <li class="menu-item"><a href="{{ route('admin.lecturers.create') }}">Register Lecturer</a></li>
            <li class="menu-item"><a href="#">Statistics of Users</a></li>
            <li class="menu-item"><a href="#">Groups</a></li>
            <li class="menu-item"><a href="#">Chatbox</a></li>
            <li class="menu-item"><a href="#">Resources</a></li>
            <li class="menu-item"><a href="#">Announcements</a></li>
        </ul>
        <div class="logout-form">
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </aside>
    <main class="main-content">
        <nav class="top-navbar"><div class="nav-title">SMART DISCUSSION FORUM</div></nav>
        <div class="dashboard-body">
            <div class="welcome-section">
                <h1 class="welcome-title">Welcome Administrator {{ Auth::user()->name }}! 👋</h1>
            </div>
            <section class="metrics-grid">
                <div class="metric-card students">
                    <div class="metric-title">Active Students</div>
                    <div class="metric-content"><div class="metric-icon-box">👥</div><div class="metric-value">120</div></div>
                    <div class="progress-container"><div class="progress-bar"></div></div>
                    <span class="metric-percentage">80% of all users</span>
                </div>
                <div class="metric-card warnings">
                    <div class="metric-title">Warning List</div>
                    <div class="metric-content"><div class="metric-icon-box">⚠️</div><div class="metric-value">18</div></div>
                    <div class="progress-container"><div class="progress-bar"></div></div>
                    <span class="metric-percentage">12% of all users</span>
                </div>
                <div class="metric-card blacklist">
                    <div class="metric-title">Blacklisted Users</div>
                    <div class="metric-content"><div class="metric-icon-box">🚫</div><div class="metric-value">7</div></div>
                    <div class="progress-container"><div class="progress-bar"></div></div>
                    <span class="metric-percentage">5% of all users</span>
                </div>
                <div class="metric-card totals">
                    <div class="metric-title">Total Users</div>
                    <div class="metric-content"><div class="metric-icon-box">📊</div><div class="metric-value">145</div></div>
                    <div class="progress-container"><div class="progress-bar"></div></div>
                    <span class="metric-percentage">100%</span>
                </div>
            </section>
            <section class="chart-card">
                <h3 class="chart-title">User Statistics Overview</h3>
                <div class="chart-layout">
                    <div class="donut-container">
                        <div class="donut-chart">
                            <div class="donut-hole"><span class="donut-total">145</span><span class="donut-label">Total Users</span></div>
                        </div>
                    </div>
                    <div class="chart-legend">
                        <div class="legend-item"><div class="legend-label-group"><div class="legend-dot dot-green"></div><span>Active Students</span></div><div>120 (80%)</div></div>
                        <div class="legend-item"><div class="legend-label-group"><div class="legend-dot dot-orange"></div><span>Warning List</span></div><div>18 (12%)</div></div>
                        <div class="legend-item"><div class="legend-label-group"><div class="legend-dot dot-red"></div><span>Blacklisted Users</span></div><div>7 (5%)</div></div>
                    </div>
                </div>
            </section>
        </div>
    </main>
</body>
</html>