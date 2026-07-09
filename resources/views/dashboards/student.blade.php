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

        /* Left Navbar - Stays sticky and visible */
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
        
        /* Active Menu State */
        .menu-item.active a { 
            color: white; 
        }

        /* Hover Highlight Block */
        .menu-item a:hover {
            color: white !important;
            background-color: #2563eb; 
        }

        .badge { background: #ef4444; color: white; font-size: 11px; padding: 2px 7px; border-radius: 20px; font-weight: 700; }
        .logout-btn { width: 100%; background: none; border: none; padding: 14px 24px; color: #f43f5e; text-align: left; font-weight: 700; font-size: 14px; cursor: pointer; margin-top: auto; border-top: 1px solid rgba(255,255,255,0.05); transition: background 0.2s; }
        .logout-btn:hover { background: rgba(244, 63, 94, 0.1); }

        /* Content Frames & View Triggers */
        .main-content { flex-grow: 1; padding: 40px; display: flex; flex-direction: column; gap: 30px; }
        .dashboard-screen { display: none; width: 100%; } /* Hidden by default */
        .dashboard-screen.active-view { display: flex; flex-direction: column; gap: 30px; } /* Toggle Target class */

        .welcome-header { margin-bottom: -10px; }
        .welcome-txt { font-size: 26px; font-weight: 700; color: #0f172a; }
        .welcome-sub { font-size: 14px; color: #64748b; margin-top: 4px; }

        /* Notification Banner Component */
        .alert-banner { background-color: #eff6ff; border-left: 4px solid #2563eb; color: #1e3a8a; padding: 16px 20px; border-radius: 8px; font-size: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; align-items: flex-start; gap: 12px; }
        .alert-icon { font-size: 18px; line-height: 1; }
        .alert-heading { margin: 0; font-weight: 700; color: #1e293b; }
        .alert-body { margin: 4px 0 0 0; color: #2563eb; font-weight: 600; }

        /* Top Grid Metrics */
        .cards-row { display: flex; gap: 20px; flex-wrap: wrap; }
        .metric-card { background: white; border-radius: 12px; padding: 20px; border: 1px solid #e2e8f0; width: 220px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        .m-title { font-size: 13px; font-weight: 700; color: #16a34a; margin-bottom: 12px; }
        .m-val { font-size: 32px; font-weight: 800; color: #0f172a; margin-bottom: 8px; }
        .m-sub { font-size: 12px; color: #64748b; }
        .progress-line { width: 100%; height: 6px; background: #e2e8f0; border-radius: 4px; margin-top: 10px; overflow: hidden; }
        .progress-fill { background: #16a34a; height: 100%; width: 85%; }

        .status-check { width: 36px; height: 36px; background: #dcfce7; color: #15803d; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin: 10px 0; }
        .btn-topic-action { background: #1d4ed8; color: white; border: none; width: 100%; padding: 10px; font-weight: 700; border-radius: 6px; font-size: 12px; cursor: pointer; margin-top: 12px; text-align: center; text-decoration: none; display: block; transition: background 0.2s; }
        .btn-topic-action:hover { background: #1e40af; }

        /* Main Workspace Split Layout */
        .dashboard-grid { display: flex; gap: 24px; flex-wrap: wrap; align-items: flex-start; }
        .left-column { flex: 1; min-width: 400px; display: flex; flex-direction: column; gap: 24px; }
        .right-column { width: 400px; display: flex; flex-direction: column; gap: 24px; }

        /* Feed Timelines & Blocks */
        .content-panel { background: white; border-radius: 12px; padding: 24px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        .panel-title { font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
        .feed-list { display: flex; flex-direction: column; gap: 18px; }
        .feed-item { display: flex; gap: 14px; padding-bottom: 14px; border-bottom: 1px solid #f1f5f9; }
        .feed-item:last-child { border: none; padding-bottom: 0; }
        .feed-avatar { width: 32px; height: 32px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #2563eb; font-size: 13px; }
        .feed-msg-title { font-size: 13px; font-weight: 700; color: #334155; }
        .feed-time { font-size: 11px; color: #94a3b8; margin-top: 2px; }
        
        .btn-view-all { width: 100%; background: #1d4ed8; color: white; border: none; padding: 12px; border-radius: 6px; font-weight: 700; font-size: 13px; cursor: pointer; margin-top: 20px; transition: background 0.2s; }
        .btn-view-all:hover { background: #1e40af; }

        /* List Items styling */
        .list-row-item { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 10px; }
        .list-row-item:last-child { margin-bottom: 0; }
        .item-info-meta { display: flex; flex-direction: column; gap: 4px; }
        .item-info-title { font-size: 14px; font-weight: 700; color: #334155; }
        .item-info-badge { font-size: 11px; color: #64748b; background: #e2e8f0; padding: 2px 6px; border-radius: 4px; font-weight: 600; width: fit-content; }
        
        /* Grade Score Tags */
        .grade-display { text-align: right; }
        .grade-score { font-size: 16px; font-weight: 800; color: #0f172a; }
        .grade-total { font-size: 12px; color: #64748b; font-weight: 500; }
        .grade-status-passed { color: #16a34a; font-size: 11px; font-weight: 700; display: block; margin-top: 2px; }
        .grade-status-failed { color: #dc2626; font-size: 11px; font-weight: 700; display: block; margin-top: 2px; }

        /* Placeholder Screen Content Styling */
        .placeholder-card { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 30px; max-width: 600px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        .placeholder-card h2 { color: #0f172a; margin-bottom: 10px; font-size: 20px; }
        .placeholder-card p { color: #64748b; font-size: 14px; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="top-brand-bar">SMART DISCUSSION FORUM</div>
    <div class="workspace-layout">
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li class="menu-item active" onclick="switchDashboardScreen(this, 'main-menu-view')"><a href="#">Main Menu</a></li>
                <li class="menu-item" onclick="switchDashboardScreen(this, 'profile-view')"><a href="#">Profile</a></li>
                <li class="menu-item" onclick="switchDashboardScreen(this, 'marks-view')"><a href="#">Marks</a></li>
                
                <li class="menu-item"><a href="{{ route('chat.index') }}">Chats</a></li>
                
                <li class="menu-item" onclick="switchDashboardScreen(this, 'notifications-view')"><a href="#">Notifications <span class="badge">3</span></a></li>
                <li class="menu-item" onclick="switchDashboardScreen(this, 'announcements-view')"><a href="#">Announcements</a></li>
            </ul>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
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

            <div id="main-menu-view" class="dashboard-screen active-view">
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
                
                <div class="dashboard-grid">
                    <div class="left-column">
                        <section style="display:flex; gap:20px; flex-wrap:wrap;">
                            <a href="{{ route('topics.create') }}" style="background:#2563eb; color:white; padding:20px; border-radius:12px; text-decoration:none; width:260px; display:flex; align-items:center; gap:15px; box-shadow:0 2px 5px rgba(0,0,0,0.1); transition:0.2s;" onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                                <div style="background:white; color:#2563eb; width:45px; height:45px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:22px;">💬</div>
                                <div>
                                    <h3 style="font-size:16px; font-weight:700; margin-bottom:5px;">Create Topic</h3>
                                    <p style="font-size:12px; opacity:0.9;">Start a new discussion</p>
                                </div>
                            </a>

                            <a href="{{ route('groups.create') }}" style="background:#10b981; color:white; padding:20px; border-radius:12px; text-decoration:none; width:260px; display:flex; align-items:center; gap:15px; box-shadow:0 2px 5px rgba(0,0,0,0.1); transition:0.2s;" onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10b981'">
                                <div style="background:white; color:#10b981; width:45px; height:45px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:22px;">👥</div>
                                <div>
                                    <h3 style="font-size:16px; font-weight:700; margin-bottom:5px;">Create Group</h3>
                                    <p style="font-size:12px; opacity:0.9;">Create a discussion group</p>
                                </div>
                            </a>
                        </section>

                        <section class="content-panel" style="margin-top: 10px;">
                            <h3 class="panel-title">✍️ Available Assessments</h3>
                            @if(isset($activeQuizzes) && count($activeQuizzes) > 0)
                                <p style="color: #10b981; font-size: 14px; margin-bottom: 15px; font-weight: 600;">✅ Your registered course streams have active evaluation windows open.</p>
                                @foreach($activeQuizzes as $activeQuiz)
                                    <div class="list-row-item">
                                        <div class="item-info-meta">
                                            <span class="item-info-title">{{ $activeQuiz->title }}</span>
                                            <span class="item-info-badge">{{ $activeQuiz->courseCode }} • {{ $activeQuiz->duration }} Mins</span>
                                        </div>
                                        <a href="{{ route('quizzes.show', ['quizID' => $activeQuiz->quizID ?? $activeQuiz->id]) }}" style="display: inline-block; padding: 8px 16px; background: #10b981; color: white; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 13px;">✍️ Attempt Quiz</a>
                                    </div>
                                @endforeach
                            @else
                                <p style="color: #64748b; font-size: 14px; margin-bottom: 15px;">No active evaluation windows are currently open for your course stream.</p>
                                <div class="list-row-item" style="opacity: 0.6; background: #f1f5f9;">
                                    <div class="item-info-meta">
                                        <span class="item-info-title" style="color: #94a3b8;">No Evaluation Scheduled</span>
                                        <span class="item-info-badge" style="background: #cbd5e1; color: #64748b;">-- • 0 Mins</span>
                                    </div>
                                    <button disabled style="display: inline-block; padding: 8px 16px; background: #94a3b8; color: #e2e8f0; border-radius: 6px; border: none; font-weight: bold; font-size: 13px; cursor: not-allowed;">🔒 Attempt Quiz</button>
                                </div>
                            @endif
                        </section>
                    </div>

                    <div class="right-column">
                        <section class="content-panel">
                            <div class="panel-title">📢 Recent Announcements</div>
                            <div class="feed-list" id="live-announcements-container">
                                <div class="feed-item">
                                    <div class="feed-avatar">D</div>
                                    <div><div class="feed-msg-title">New quiz available: Software Engineering</div><div class="feed-time">2 hours ago</div></div>
                                </div>
                                <div class="feed-item">
                                    <div class="feed-avatar">D</div>
                                    <div><div class="feed-msg-title">Department meeting on Friday</div><div class="feed-time">1 day ago</div></div>
                                </div>
                            </div>
                            <button class="btn-view-all" onclick="document.querySelector('[onclick*=\'announcements-view\']').click()">VIEW ALL</button>
                        </section>
                    </div>
                </div>
            </div>

            <div id="profile-view" class="dashboard-screen">
                <div class="welcome-header">
                    <h1 class="welcome-txt">My Profile</h1>
                    <p class="welcome-sub">Manage your account information and preferences.</p>
                </div>
                <div class="placeholder-card" style="max-width: 100%;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">
                        <h2 style="font-size: 18px;">Account Overview</h2>
                        <a href="{{ route('profile.edit') }}" style="padding: 6px 14px; background: #2563eb; color: white; text-decoration: none; border-radius: 6px; font-size: 12px; font-weight: 700;">⚙️ Edit Profile</a>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px;">
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 4px;">Full Name</label>
                            <div style="font-size: 15px; font-weight: 600; color: #0f172a;">{{ Auth::user()->name }}</div>
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 4px;">Email Address</label>
                            <div style="font-size: 15px; font-weight: 600; color: #0f172a;">{{ Auth::user()->email }}</div>
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 4px;">Username</label>
                            <div style="font-size: 15px; font-weight: 600; color: #0f172a;">{{ Auth::user()->username ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 4px;">Phone Connection</label>
                            <div style="font-size: 15px; font-weight: 600; color: #0f172a;">{{ Auth::user()->phone ?? 'No Phone Number' }}</div>
                        </div>
                    </div>
                    
                    <h2 style="margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; margin-top: 20px; font-size: 18px;">Preferences & Settings</h2>
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <label style="display: flex; align-items: center; gap: 10px; font-size: 14px; color: #334155; cursor: pointer;">
                            <input type="checkbox" checked style="accent-color: #2563eb;"> Real-time browser notifications for chat messages
                        </label>
                        <label style="display: flex; align-items: center; gap: 10px; font-size: 14px; color: #334155; cursor: pointer;">
                            <input type="checkbox" checked style="accent-color: #2563eb;"> Email summaries for unanswered forum topics
                        </label>
                    </div>
                </div>
            </div>

            <div id="marks-view" class="dashboard-screen">
                <div class="welcome-header">
                    <h1 class="welcome-txt">Academic Standing</h1>
                    <p class="welcome-sub">Track your grading records and continuous assessment progress.</p>
                </div>
                
                <section id="grades-section" class="content-panel">
                    <h3 class="panel-title">📊 Assessment Performance & Marks</h3>
                    @if(isset($completedQuizzes) && count($completedQuizzes) > 0)
                        <p style="color: #64748b; font-size: 14px; margin-bottom: 15px;">Review your scores and feedback from completed assessments.</p>
                        @foreach($completedQuizzes as $quiz)
                            <div class="list-row-item">
                                <div class="item-info-meta">
                                    <span class="item-info-title">{{ $quiz->title }}</span>
                                    <span class="item-info-badge">{{ $quiz->courseCode ?? 'Course' }} • Completed {{ \Carbon\Carbon::parse($quiz->pivot->updated_at ?? now())->diffForHumans() }}</span>
                                </div>
                                <div class="grade-display">
                                    <span class="grade-score">{{ $quiz->pivot->score ?? $quiz->score }}</span>
                                    <span class="grade-total">/ {{ $quiz->total_marks ?? 100 }}</span>
                                    @if(($quiz->pivot->score ?? $quiz->score) >= ($quiz->passing_marks ?? 50))
                                        <span class="grade-status-passed">Passed ✓</span>
                                    @else
                                        <span class="grade-status-failed">Failed ✕</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p style="color: #64748b; font-size: 14px; margin-bottom: 15px;">No completed evaluation results are registered to your gradebook yet.</p>
                        <div class="list-row-item" style="opacity: 0.6; background: #f1f5f9; justify-content: center; padding: 20px;">
                            <span style="color: #94a3b8; font-size: 13px; font-weight: 600;">No Academic Transcripts Formed</span>
                        </div>
                    @endif
                </section>
            </div>

            <div id="notifications-view" class="dashboard-screen">
                <div class="welcome-header"><h1 class="welcome-txt">Notifications</h1></div>
                <div class="placeholder-card"><h2>Alert Logs</h2><p>Real-time notifications will stream live here.</p></div>
            </div>

            <div id="announcements-view" class="dashboard-screen">
                <div class="welcome-header"><h1 class="welcome-txt">All Announcements</h1></div>
                <div class="placeholder-card"><h2>Notice Board</h2><p>Historical university announcements are tracked inside this module panel frame.</p></div>
            </div>

        </main>
    </div>

    <script>
        function switchDashboardScreen(element, targetScreenId) {
            // Remove active classes from sidebar items
            document.querySelectorAll('.menu-item').forEach(item => item.classList.remove('active'));
            // Add active class to clicked sidebar item
            element.classList.add('active');

            // Hide all dashboard view screens
            document.querySelectorAll('.dashboard-screen').forEach(screen => screen.classList.remove('active-view'));
            // Display targeted view screen
            document.getElementById(targetScreenId).classList.add('active-view');
        }
    </script>
</body>
</html>