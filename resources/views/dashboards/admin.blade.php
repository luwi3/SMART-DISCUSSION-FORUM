<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Smart Discussion Forum</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background-color: #f0f4f9; min-height: 100vh; display: flex; }
        .sidebar { width: 260px; background-color: #032b88; color: white; display: flex; flex-direction: column; flex-shrink: 0; }
        .sidebar-brand { padding: 30px 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .brand-icon { width: 44px; height: 44px; color: white; margin-bottom: 10px; }
        .brand-text { font-size: 14px; font-weight: 900; letter-spacing: 0.5px; }
        .sidebar-menu { list-style: none; padding: 20px 0; flex-grow: 1; display: flex; flex-direction: column; gap: 4px; }
        .menu-item a { display: flex; align-items: center; gap: 14px; padding: 12px 24px; color: rgba(255,255,255,0.7); text-decoration: none; font-size: 14px; font-weight: 600; transition: background 0.2s ease, color 0.2s ease, padding-left 0.2s ease; border-radius: 0 50px 50px 0; margin-right: 14px; }
        .menu-item a:hover { color: white; background: rgba(255,255,255,0.08); padding-left: 30px; }
        .menu-item.active a { color: white; background: #2563eb; font-weight: 700; }
        .menu-item.active a:hover { background: #2563eb; padding-left: 24px; }
        .logout-form { border-top: 1px solid rgba(255,255,255,0.1); padding: 15px 0; }
        .logout-btn { width: 100%; background: none; border: none; display: flex; align-items: center; gap: 14px; padding: 12px 24px; color: #f43f5e; font-size: 14px; font-weight: 700; cursor: pointer; transition: background 0.2s ease; }
        .logout-btn:hover { background: rgba(244, 63, 94, 0.1); }
        .mobile-menu-toggle { transition: transform 0.15s ease, color 0.15s ease; }
        .mobile-menu-toggle:hover { color: #2563eb; transform: scale(1.1); }
        .main-content { flex-grow: 1; display: flex; flex-direction: column; min-width: 0; }
        .top-navbar { background: white; padding: 18px 30px; display: flex; align-items: center; border-bottom: 1px solid #e2e8f0; }
        .nav-title { font-size: 15px; font-weight: 800; color: #1e293b; }
        .dashboard-body { padding: 30px; max-width: 1300px; width: 100%; margin: 0 auto; }
        .welcome-section { margin-bottom: 24px; }
        .welcome-title { font-size: 24px; font-weight: 700; color: #0f172a; }
        .metrics-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 24px; }
        .metric-card { 
    background: white; 
    border-radius: 12px; 
    padding: 20px; 
    border: 1px solid #eef2f6;
    /* Smooth out the movement over 0.3 seconds */
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
}

/* Define the 3D interaction state */
.metric-card:hover {
    transform: translateY(-5px); /* Smoothly lifts the card up */
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08); /* Adds depth with a soft shadow */
}
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

        .back-link { display: inline-flex; align-items: center; gap: 6px; margin-bottom: 16px; font-size: 13px; font-weight: 700; color: #2563eb; text-decoration: none; }
        .back-link .arrow { display: inline-block; transition: transform 0.15s ease; }
        .back-link:hover .arrow { transform: translateX(-4px); }
        .back-link:hover { text-decoration: underline; }
        .alert-banner { background: #f0fdf4; border-left: 4px solid #16a34a; color: #166534; padding: 12px 16px; border-radius: 8px; font-size: 14px; font-weight: 600; margin-bottom: 20px; }
        .data-table { width: 100%; border-collapse: collapse; text-align: left; }
        .data-table th { padding: 12px; border-bottom: 2px solid #eef2f6; color: #64748b; font-size: 13px; }
        .data-table td { padding: 12px; border-bottom: 1px solid #eef2f6; font-size: 14px; color: #1e293b; }
        .data-table tbody tr { transition: background 0.15s ease; }
        .data-table tbody tr:hover { background: #f8fafc; }
        .status-pill { padding: 4px 8px; border-radius: 4px; font-size: 13px; font-weight: 600; }
        .status-active { color: #16a34a; background: #f0fdf4; }
        .status-warning { color: #ea580c; background: #fff7ed; }
        .status-blacklisted { color: #dc2626; background: #fef2f2; }
        .remove-btn { background: #dc2626; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 600; transition: background 0.15s ease, transform 0.1s ease; }
        .remove-btn:hover { background: #b91c1c; }
        .remove-btn:active { transform: scale(0.96); }
        .peak-stats-row { display: flex; gap: 20px; margin-bottom: 20px; flex-wrap: wrap; }
        .peak-stat-box { background: #eff6ff; border-radius: 10px; padding: 16px 20px; flex: 1; min-width: 200px; }
        .peak-stat-label { font-size: 12px; font-weight: 700; color: #2563eb; text-transform: uppercase; margin-bottom: 6px; }
        .peak-stat-value { font-size: 20px; font-weight: 800; color: #0f172a; }
        .metric-card-link { text-decoration: none; color: inherit; display: block; }
        .course-block { margin-bottom: 28px; transition: box-shadow 0.2s ease; }
        .course-block:hover { box-shadow: 0 6px 16px rgba(0,0,0,0.06); }
        .course-header { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 14px; }
        .course-name { font-size: 16px; font-weight: 800; color: #0f172a; }
        .course-summary { display: flex; gap: 16px; font-size: 12px; color: #64748b; font-weight: 600; }
        .course-summary span strong { color: #1e293b; }
        .rank-badge { display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 50%; font-size: 12px; font-weight: 800; margin-right: 8px; transition: transform 0.15s ease; }
        .data-table tbody tr:hover .rank-badge { transform: scale(1.12); }
        .rank-1 { background: #fef9c3; color: #a16207; }
        .rank-2 { background: #e5e7eb; color: #4b5563; }
        .rank-3 { background: #fed7aa; color: #9a3412; }
        .rank-other { background: #eff6ff; color: #2563eb; }
        .zero-activity { color: #cbd5e1; font-style: italic; }

        .mobile-menu-toggle { display: none; background: none; border: none; font-size: 20px; cursor: pointer; margin-right: 16px; padding: 4px; color: #1e293b; }
        .sidebar-backdrop { display: none; position: fixed; inset: 0; background: rgba(3, 43, 136, 0.35); z-index: 900; }
        .sidebar-backdrop.active { display: block; }

        @media (max-width: 900px) {
            .metrics-grid { grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); }
            .chart-layout { flex-direction: column; align-items: flex-start; gap: 20px; }
        }

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
                box-shadow: 6px 0 24px rgba(0,0,0,0.25);
            }
            .sidebar.mobile-open { transform: translateX(0); }
            .dashboard-body { padding: 18px; }
            .top-navbar { padding: 14px 18px; }
        }
    </style>
</head>
<body>
    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <svg class="brand-icon" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>
            <div class="brand-text">SMART DISCUSSION FORUM</div>
        </div>
        <ul class="sidebar-menu">

            <li class="menu-item active"><a href="?view=dashboard">Dashboard</a></li>
            

            
            <li class="menu-item"><a href="{{ route('profile.edit') }}">Profile</a></li>
            
            <li class="menu-item"><a href="{{ route('admin.lecturers.create') }}">Register Lecturer</a></li>
            <li class="menu-item"><a href="?view=lecturers">Manage Lecturers</a></li>
            <li class="menu-item"><a href="?view=courses">Course Engagement</a></li>
            <li class="menu-item"><a href="?view=blacklist">Activate Student</a></li>
            <!-- <li class="menu-item"><a href="#">Chatbox</a></li> -->
            <!-- <li class="menu-item"><a href="#">Resources</a></li> -->
            <!-- <li class="menu-item"><a href="#">Announcements</a></li> -->
        </ul>
        <div class="logout-form">
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </aside>
   <main class="main-content">
    <nav class="top-navbar">
        <button type="button" class="mobile-menu-toggle" id="mobile-menu-toggle" aria-label="Open menu">☰</button>
        <div class="nav-title">SMART DISCUSSION FORUM</div>
    </nav>
    <div class="dashboard-body">

        @switch(request('view'))
            @case('blacklist')
                <div class="welcome-section">
                    <h1 class="welcome-title">Manage Suspended Students 🚫</h1>
                </div>
                
                <section class="chart-card">
                    <h3 class="chart-title">Suspended Accounts List</h3>
                    
                    <div style="overflow-x: auto; margin-top: 20px;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left;">
                            <thead>
                                <tr style="border-bottom: 2px solid #eef2f6; color: #64748b;">
                                    <th style="padding: 12px;">User ID</th>
                                    <th style="padding: 12px;">Name</th>
                                    <th style="padding: 12px;">Status</th>
                                    <th style="padding: 12px;">Action</th>
                                </tr>
                            </thead>
                           <tbody>
    @forelse($suspendedStudents as $student)
        <tr style="border-bottom: 1px solid #eef2f6;">
    <td style="padding: 12px;">#{{ $student->regNo }}</td>
    <td style="padding: 12px;">{{ $student->user->name ?? 'No Name Linked' }}</td>
    <td style="padding: 12px;">
        <span style="color: #ef4444; background: #fee2e2; padding: 4px 8px; border-radius: 4px; font-size: 14px;">
            {{ $student->status }}
        </span>
    </td>
    <td style="padding: 12px;">
    <form action="/admin/students/{{ $student->regNo }}/activate" method="POST" style="margin: 0;">
            @csrf
            <button type="submit" style="background: #10b981; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer;">
                Activate
            </button>
        </form>
    </td>
</tr>
    @empty
        <tr>
            <td colspan="4" style="padding: 20px; text-align: center; color: #64748b;">
                No blacklisted students found. 🎉
            </td>
        </tr>
    @endforelse
</tbody>
                        </table>
                    </div>
                </section>
                @break

            @default
                <div class="welcome-section">
                    <h1 class="welcome-title">Welcome Administrator {{ Auth::user()->name }}! 👋</h1>
                </div>

                @if(session('success'))
                    <div class="alert-banner">{{ session('success') }}</div>
                @endif
                
                <section class="metrics-grid">
                    <a href="?view=active" class="metric-card students metric-card-link">
                        <div class="metric-title">Active Students</div>
                        <div class="metric-content"><div class="metric-icon-box">👥</div><div class="metric-value">{{ $activeStudents }}</div></div>
                        <div class="progress-container"><div class="progress-bar" style="width: {{ $activePercentage }}%;"></div></div>
                        <span class="metric-percentage">Students marked active — click to view names</span>
                    </a>
                    
                    <a href="?view=warning" class="metric-card warnings metric-card-link">
                        <div class="metric-title">Warning List</div>
                        <div class="metric-content"><div class="metric-icon-box">⚠️</div><div class="metric-value">{{ $warningList }}</div></div>
                        <div class="progress-container"><div class="progress-bar" style="width: {{ $warningPercentage }}%;"></div></div>
                        <span class="metric-percentage">Students flagged on warning — click to view names</span>
                    </a>
                    
                    <a href="?view=blacklist" class="metric-card blacklist metric-card-link">
                        <div class="metric-title">Blacklisted Users</div>
                        <div class="metric-content">
                            <div class="metric-icon-box">🚫</div>
                            <div class="metric-value">{{ $blacklistedUsers }}</div>
                        </div>
                        <div class="progress-container"><div class="progress-bar" style="width: {{ $blacklistedPercentage }}%;"></div></div>
                        <span class="metric-percentage">Suspended accounts — click to view names</span>
                    </a>
                    
                    <a href="?view=all" class="metric-card totals metric-card-link">
                        <div class="metric-title">Total Users</div>
                        <div class="metric-content"><div class="metric-icon-box">📊</div><div class="metric-value">{{ $totalUsers }}</div></div>
                        <div class="progress-container"><div class="progress-bar"></div></div>
                        <span class="metric-percentage">100% of registrations — click to view names</span>
                    </a>
                </section>

                <section class="chart-card" style="margin-bottom: 24px;">
                    <h3 class="chart-title">User Statistics Overview</h3>
                    <div class="chart-layout">
                        <div class="donut-container">
                            <div class="donut-chart" style="background: conic-gradient(#16a34a 0% {{ $activePercentage }}%, #ea580c {{ $activePercentage }}% {{ $activePercentage + $warningPercentage }}%, #dc2626 {{ $activePercentage + $warningPercentage }}% 100%);">
                                <div class="donut-hole"><span class="donut-total">{{ $totalUsers }}</span><span class="donut-label">Total Users</span></div>
                            </div>
                        </div>
                        <div class="chart-legend">
                            <div class="legend-item"><div class="legend-label-group"><div class="legend-dot dot-green"></div><span>Active Students</span></div><div>{{ $activeStudents }}</div></div>
                            <div class="legend-item"><div class="legend-label-group"><div class="legend-dot dot-orange"></div><span>Warning List</span></div><div>{{ $warningList }}</div></div>
                            <div class="legend-item"><div class="legend-label-group"><div class="legend-dot dot-red"></div><span>Blacklisted Users</span></div><div>{{ $blacklistedUsers }}</div></div>
                        </div>
                    </div>
                </section>

                <section class="chart-card" style="max-width: 100%;">
                    <h3 class="chart-title">📈 Forum Activity — Peak Usage Times</h3>

                    @if($totalMessages > 0)
                        <div class="peak-stats-row">
                            <div class="peak-stat-box">
                                <div class="peak-stat-label">Busiest Hour</div>
                                <div class="peak-stat-value">
                                    {{ \Carbon\Carbon::createFromTime($peakHour)->format('g A') }}
                                    <span style="font-size: 13px; font-weight: 600; color: #64748b;">({{ $peakHourCount }} messages)</span>
                                </div>
                            </div>
                            <div class="peak-stat-box">
                                <div class="peak-stat-label">Busiest Day</div>
                                <div class="peak-stat-value">
                                    {{ $peakDay }}
                                    <span style="font-size: 13px; font-weight: 600; color: #64748b;">({{ $peakDayCount }} messages)</span>
                                </div>
                            </div>
                            <div class="peak-stat-box">
                                <div class="peak-stat-label">Total Messages</div>
                                <div class="peak-stat-value">{{ $totalMessages }}</div>
                            </div>
                        </div>

                        <canvas id="peakActivityChart" height="90"></canvas>
                    @else
                        <p style="color: #64748b; font-size: 14px;">No message activity recorded yet — this chart will populate once students start posting.</p>
                    @endif
                </section>
                @break

            @case('lecturers')
                <a href="?view=dashboard" class="back-link"><span class="arrow">←</span> Back to Dashboard</a>
                <div class="welcome-section">
                    <h1 class="welcome-title">Manage Lecturers 👨‍🏫</h1>
                </div>

                @if(session('success'))
                    <div class="alert-banner">{{ session('success') }}</div>
                @endif

                <section class="chart-card">
                    <h3 class="chart-title">Registered Lecturer Accounts</h3>
                    <div style="overflow-x: auto; margin-top: 20px;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Staff No</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Department</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lecturers as $lecturer)
                                    <tr>
                                        <td>#{{ $lecturer->staffNo }}</td>
                                        <td>{{ $lecturer->user->name ?? 'No Name Linked' }}</td>
                                        <td>{{ $lecturer->user->email ?? '—' }}</td>
                                        <td>{{ $lecturer->department }}</td>
                                        <td>
                                            <form action="{{ route('admin.lecturers.destroy', $lecturer->staffNo) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Remove this lecturer account? This cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="remove-btn">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" style="padding: 20px; text-align: center; color: #64748b;">No lecturer accounts registered yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
                @break

            @case('courses')
                <a href="?view=dashboard" class="back-link"><span class="arrow">←</span> Back to Dashboard</a>
                <div class="welcome-section">
                    <h1 class="welcome-title">Course Engagement 📚</h1>
                </div>

                @forelse($courseActivity as $courseCode => $courseData)
                    <section class="chart-card course-block">
                        <div class="course-header">
                            <div class="course-name">{{ $courseCode }}</div>
                            <div class="course-summary">
                                <span>Students: <strong>{{ $courseData['student_count'] }}</strong></span>
                                <span>Total messages: <strong>{{ $courseData['total_messages'] }}</strong></span>
                                <span>Avg per student: <strong>{{ $courseData['average'] }}</strong></span>
                            </div>
                        </div>

                        <div style="overflow-x: auto;">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Reg No</th>
                                        <th>Name</th>
                                        <th>Messages Sent</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($courseData['students'] as $index => $student)
                                        <tr>
                                            <td>
                                                @php
                                                    $rank = $index + 1;
                                                    $rankClass = match(true) {
                                                        $rank === 1 => 'rank-1',
                                                        $rank === 2 => 'rank-2',
                                                        $rank === 3 => 'rank-3',
                                                        default => 'rank-other',
                                                    };
                                                @endphp
                                                <span class="rank-badge {{ $rankClass }}">{{ $rank }}</span>
                                            </td>
                                            <td>#{{ $student->regNo }}</td>
                                            <td>{{ $student->name }}</td>
                                            <td>
                                                @if($student->message_count > 0)
                                                    {{ $student->message_count }}
                                                @else
                                                    <span class="zero-activity">0 — no activity yet</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>
                @empty
                    <section class="chart-card">
                        <p style="color: #64748b; font-size: 14px;">No students with an assigned course code found yet.</p>
                    </section>
                @endforelse
                @break

            @case('active')
                <a href="?view=dashboard" class="back-link"><span class="arrow">←</span> Back to Dashboard</a>
                <div class="welcome-section">
                    <h1 class="welcome-title">Active Students 👥</h1>
                </div>

                <section class="chart-card">
                    <h3 class="chart-title">Currently Active Accounts</h3>
                    <div style="overflow-x: auto; margin-top: 20px;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Reg No</th>
                                    <th>Name</th>
                                    <th>Course</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activeStudentsList as $student)
                                    <tr>
                                        <td>#{{ $student->regNo }}</td>
                                        <td>{{ $student->user->name ?? 'No Name Linked' }}</td>
                                        <td>{{ $student->courseCode ?? '—' }}</td>
                                        <td><span class="status-pill status-active">{{ $student->status }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" style="padding: 20px; text-align: center; color: #64748b;">No active students found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
                @break

            @case('warning')
                <a href="?view=dashboard" class="back-link"><span class="arrow">←</span> Back to Dashboard</a>
                <div class="welcome-section">
                    <h1 class="welcome-title">Students on Warning ⚠️</h1>
                </div>

                <section class="chart-card">
                    <h3 class="chart-title">Flagged Accounts</h3>
                    <div style="overflow-x: auto; margin-top: 20px;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Reg No</th>
                                    <th>Name</th>
                                    <th>Course</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($warningStudentsList as $student)
                                    <tr>
                                        <td>#{{ $student->regNo }}</td>
                                        <td>{{ $student->user->name ?? 'No Name Linked' }}</td>
                                        <td>{{ $student->courseCode ?? '—' }}</td>
                                        <td><span class="status-pill status-warning">{{ $student->status }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" style="padding: 20px; text-align: center; color: #64748b;">No students currently on warning. 🎉</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
                @break

            @case('all')
                <a href="?view=dashboard" class="back-link"><span class="arrow">←</span> Back to Dashboard</a>
                <div class="welcome-section">
                    <h1 class="welcome-title">All Registered Users 📊</h1>
                </div>

                <section class="chart-card">
                    <h3 class="chart-title">Every Account on the Platform</h3>
                    <div style="overflow-x: auto; margin-top: 20px;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allUsersList as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td style="text-transform: capitalize;">{{ $user->role }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" style="padding: 20px; text-align: center; color: #64748b;">No users found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
                @break
        @endswitch

    </div>
</main>

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

        const chartCanvas = document.getElementById('peakActivityChart');
        if (chartCanvas && typeof Chart !== 'undefined') {
            const hourlyActivity = @json($hourlyActivity ?? []);
            const labels = Object.keys(hourlyActivity).map(function (hour) {
                const h = parseInt(hour, 10);
                const period = h >= 12 ? 'PM' : 'AM';
                const display = h % 12 === 0 ? 12 : h % 12;
                return display + period;
            });
            const data = Object.values(hourlyActivity);

            new Chart(chartCanvas, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Messages sent',
                        data: data,
                        backgroundColor: '#2563eb',
                        borderRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } }
                    }
                }
            });
        }
    });
</script>
</body>
</html>