<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Smart Discussion Forum</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background-color: #f8fafc; min-height: 100vh; display: flex; flex-direction: column; }
        
        .top-brand-bar { background-color: #0c2340; color: white; padding: 18px 30px; font-weight: 700; font-size: 16px; letter-spacing: 0.5px; }
        .workspace-layout { display: flex; flex-grow: 1; }

        /* Left Navbar */
        .sidebar { width: 260px; background-color: #0a1931; color: white; display: flex; flex-direction: column; flex-shrink: 0; }
        .sidebar-menu { list-style: none; padding: 24px 0; }
        .menu-item a { display: flex; align-items: center; justify-content: space-between; padding: 14px 24px; color: #94a3b8; text-decoration: none; font-size: 14px; font-weight: 600; }
        .menu-item.active a { color: white; background: #2563eb; }
        .badge { background: #ef4444; color: white; font-size: 11px; padding: 2px 7px; border-radius: 20px; font-weight: 700; }
        .logout-btn { width: 100%; background: none; border: none; padding: 14px 24px; color: #f43f5e; text-align: left; font-weight: 700; font-size: 14px; cursor: pointer; margin-top: auto; border-top: 1px solid rgba(255,255,255,0.05); }

        /* Dashboard Container */
        .main-content { flex-grow: 1; padding: 40px; }
        .welcome-header { margin-bottom: 24px; }
        .welcome-txt { font-size: 26px; font-weight: 700; color: #0f172a; }
        .welcome-sub { font-size: 14px; color: #64748b; margin-top: 4px; }

        /* Notification Banner Component */
        .alert-banner { background-color: #eff6ff; border-left: 4px solid #2563eb; color: #1e3a8a; padding: 16px 20px; border-radius: 8px; margin-bottom: 24px; font-size: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; align-items: flex-start; gap: 12px; }
        .alert-icon { font-size: 18px; line-height: 1; }
        .alert-heading { margin: 0; font-weight: 700; color: #1e293b; }
        .alert-body { margin: 4px 0 0 0; color: #2563eb; font-weight: 600; }

        /* Top Grid Metrics */
        .cards-row { display: flex; gap: 20px; margin-bottom: 30px; }
        .metric-card { background: white; border-radius: 12px; padding: 20px; border: 1px solid #e2e8f0; width: 220px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        .m-title { font-size: 13px; font-weight: 700; color: #16a34a; margin-bottom: 12px; }
        .m-val { font-size: 32px; font-weight: 800; color: #0f172a; margin-bottom: 8px; }
        .m-sub { font-size: 12px; color: #64748b; }
        .progress-line { width: 100%; height: 6px; background: #e2e8f0; border-radius: 4px; margin-top: 10px; overflow: hidden; }
        .progress-fill { background: #16a34a; height: 100%; width: 85%; }

        .status-check { width: 36px; height: 36px; background: #dcfce7; color: #15803d; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin: 10px 0; }
        .btn-topic-action { background: #1d4ed8; color: white; border: none; width: 100%; padding: 10px; font-weight: 700; border-radius: 6px; font-size: 12px; cursor: pointer; margin-top: 12px; text-align: center; text-decoration: none; display: block; }

        /* Feed Timelines */
        .feed-container { background: white; border-radius: 12px; padding: 24px; border: 1px solid #e2e8f0; max-width: 460px; }
        .feed-title { font-size: 15px; font-weight: 700; color: #1e293b; margin-bottom: 20px; }
        .feed-list { display: flex; flex-direction: column; gap: 18px; }
        .feed-item { display: flex; gap: 14px; padding-bottom: 14px; border-bottom: 1px solid #f1f5f9; }
        .feed-item:last-child { border: none; padding-bottom: 0; }
        .feed-avatar { width: 32px; height: 32px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #2563eb; font-size: 13px; }
        .feed-msg-title { font-size: 13px; font-weight: 700; color: #334155; }
        .feed-time { font-size: 11px; color: #94a3b8; margin-top: 2px; }
        .btn-view-all { width: 100%; background: #1d4ed8; color: white; border: none; padding: 12px; border-radius: 6px; font-weight: 700; font-size: 13px; cursor: pointer; margin-top: 20px; }

        /* Assessment Card List Items styling */
        .quiz-row-item { display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; margin-bottom: 10px; }
        .quiz-info-meta { display: flex; flex-direction: column; gap: 4px; }
        .quiz-info-title { font-size: 14px; font-weight: 700; color: #334155; }
        .quiz-info-badge { font-size: 11px; color: #64748b; background: #e2e8f0; padding: 2px 6px; border-radius: 4px; font-weight: 600; width: fit-content; }
    </style>
</head>
<body>
    <div class="top-brand-bar">SMART DISCUSSION FORUM</div>
    <div class="workspace-layout">
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li class="menu-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                    <a href="{{ route('profile.edit') }}">Profile</a>
                </li>
                
                <!-- Dynamic Marks Action Button mapping natively to evaluation controller gradebook sheets -->
                <li class="menu-item">
                    @if(isset($activeQuizzes) && count($activeQuizzes) > 0)
                        <a href="{{ url('/quizzes/' . ($activeQuizzes[0]->quizID ?? $activeQuizzes[0]->id) . '/grades') }}">Marks</a>
                    @else
                        <a href="#" onclick="alert('No completed assessments or marks are available yet.')">Marks</a>
                    @endif
                </li>
                
                <li class="menu-item"><a href="{{ route('chat.index') }}">Chats</a></li>
                <li class="menu-item"><a href="#">Notifications <span class="badge">3</span></a></li>
                <li class="menu-item"><a href="#">Announcements</a></li>
            </ul>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </aside>
        
        <main class="main-content">
            @if(session('quiz_result'))
                <div class="alert-banner">
                    <span class="alert-icon">🔔</span>
                    <div>
                        <h4 class="alert-heading">Evaluation Center Update</h4>
                        <p class="alert-body">{{ session('quiz_result') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert-banner" style="background-color: #fef2f2; border-left-color: #ef4444; color: #991b1b;">
                    <span class="alert-icon">⚠️</span>
                    <div>
                        <h4 class="alert-heading" style="color: #991b1b;">System Notice</h4>
                        <p class="alert-body" style="color: #ef4444;">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <div class="welcome-header">
                <h1 class="welcome-txt">Hello, {{ Auth::user()->name }}! 👋</h1>
                <p class="welcome-sub">Always keep learning and stay active.</p>
            </div>

            <section class="cards-row">
                <div class="metric-card">
                    <div class="m-title">Participation Marks</div>
                    <div class="m-val">85%</div>
                    <div class="m-sub">Great Job! 🚀</div>
                    <div class="progress-line"><div class="progress-fill"></div></div>
                </div>
                <div class="metric-card" style="width:160px;">
                    <div class="m-title" style="color:#1e293b;">Status</div>
                    <div class="status-check">✓</div>
                    <div class="m-sub">Account Active</div>
                </div>
                <div class="metric-card" style="width:240px;">
                    <div class="m-title" style="color:#7c3aed;">Recommended Topic</div>
                    <div class="m-val" style="font-size:18px; margin-top:10px; margin-bottom:15px;">Database Design</div>
                    <a href="#" class="btn-topic-action">VIEW TOPICS</a>
                </div>
            </section>

            <section class="feed-container">
                <div class="feed-title">Recent Announcements</div>
                <div class="feed-list">
                    <div class="feed-item">
                        <div class="feed-avatar">D</div>
                        <div><div class="feed-msg-title">New quiz available: Software Engineering</div><div class="feed-time">2 hours ago</div></div>
                    </div>
                    <div class="feed-item">
                        <div class="feed-avatar">D</div>
                        <div><div class="feed-msg-title">Department meeting on Friday</div><div class="feed-time">1 day ago</div></div>
                    </div>
                    <div class="feed-item">
                        <div class="feed-avatar">D</div>
                        <div><div class="feed-msg-title">Submit your group project</div><div class="feed-time">2 days ago</div></div>
                    </div>
                </div>
                <button class="btn-view-all">VIEW ALL</button>
            </section>
            
            <!-- ✍️ Available Assessments Block -->
            <div style="background: white; padding: 20px; border-radius: 8px; margin-top: 20px; border: 1px solid #e2e8f0;">
                <h3 style="color: #1e293b; margin-bottom: 10px;">✍️ Available Assessments</h3>
                
                @if(isset($activeQuizzes) && count($activeQuizzes) > 0)
                    <!-- CASE 1: Active quizzes found for the student -->
                    <p style="color: #10b981; font-size: 14px; margin-bottom: 15px; font-weight: 600;">✅ Your registered course streams have active evaluation windows open.</p>
                    
                    @foreach($activeQuizzes as $activeQuiz)
                        <div class="quiz-row-item">
                            <div class="quiz-info-meta">
                                <span class="quiz-info-title">{{ $activeQuiz->title }}</span>
                                <span class="quiz-info-badge">{{ $activeQuiz->courseCode }} • {{ $activeQuiz->duration }} Mins</span>
                            </div>
                            <a href="{{ route('quizzes.show', ['quizID' => $activeQuiz->quizID ?? $activeQuiz->id]) }}" style="display: inline-block; padding: 8px 16px; background: #10b981; color: white; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 13px;">
                                ✍️ Attempt Quiz
                            </a>
                        </div>
                    @endforeach
                @else
                    <!-- CASE 2: No active quizzes are available right now -->
                    <p style="color: #64748b; font-size: 14px; margin-bottom: 15px;">No active evaluation windows are currently open for your course stream.</p>
                    
                    <div class="quiz-row-item" style="opacity: 0.6; background: #f1f5f9;">
                        <div class="quiz-info-meta">
                            <span class="quiz-info-title" style="color: #94a3b8;">No Evaluation Scheduled</span>
                            <span class="quiz-info-badge" style="background: #cbd5e1; color: #64748b;">-- • 0 Mins</span>
                        </div>
                        <button disabled style="display: inline-block; padding: 8px 16px; background: #94a3b8; color: #e2e8f0; border-radius: 6px; border: none; font-weight: bold; font-size: 13px; cursor: not-allowed;">
                            🔒 Attempt Quiz
                        </button>
                    </div>
                @endif
            </div>
        </main>
    </div>
</body>
</html>