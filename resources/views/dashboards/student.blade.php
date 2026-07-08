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

        /* Content Frames */
        .main-content { flex-grow: 1; padding: 40px; }
        .dashboard-screen { display: none; } /* Hidden by default */
        .dashboard-screen.active-view { display: block; } /* Toggle Target class */

        .welcome-header { margin-bottom: 24px; }
        .welcome-txt { font-size: 26px; font-weight: 700; color: #0f172a; }
        .welcome-sub { font-size: 14px; color: #64748b; margin-top: 4px; }

        /* Dashboard Cards & Layout Elements */
        .cards-row { display: flex; gap: 20px; margin-bottom: 30px; }
        .metric-card { background: white; border-radius: 12px; padding: 20px; border: 1px solid #e2e8f0; width: 220px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        .m-title { font-size: 13px; font-weight: 700; color: #16a34a; margin-bottom: 12px; }
        .m-val { font-size: 32px; font-weight: 800; color: #0f172a; margin-bottom: 8px; }
        .m-sub { font-size: 12px; color: #64748b; }
        .progress-line { width: 100%; height: 6px; background: #e2e8f0; border-radius: 4px; margin-top: 10px; overflow: hidden; }
        .progress-fill { background: #16a34a; height: 100%; width: 85%; }

        .status-check { width: 36px; height: 36px; background: #dcfce7; color: #15803d; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin: 10px 0; }
        .btn-topic-action { background: #1d4ed8; color: white; border: none; width: 100%; padding: 10px; font-weight: 700; border-radius: 6px; font-size: 12px; cursor: pointer; margin-top: 12px; text-align: center; text-decoration: none; display: block; transition: background 0.2s; }
        .btn-topic-action:hover { background: #1e40af; }

        /* Feeds and Informational Cards */
        .feed-container { background: white; border-radius: 12px; padding: 24px; border: 1px solid #e2e8f0; max-width: 460px; }
        .feed-title { font-size: 15px; font-weight: 700; color: #1e293b; margin-bottom: 20px; }
        .feed-list { display: flex; flex-direction: column; gap: 18px; }
        .feed-item { display: flex; gap: 14px; padding-bottom: 14px; border-bottom: 1px solid #f1f5f9; }
        .feed-item:last-child { border: none; padding-bottom: 0; }
        .feed-avatar { width: 32px; height: 32px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #2563eb; font-size: 13px; }
        .feed-msg-title { font-size: 13px; font-weight: 700; color: #334155; }
        .feed-time { font-size: 11px; color: #94a3b8; margin-top: 2px; }
        .btn-view-all { width: 100%; background: #1d4ed8; color: white; border: none; padding: 12px; border-radius: 6px; font-weight: 700; font-size: 13px; cursor: pointer; margin-top: 20px; transition: background 0.2s; }
        .btn-view-all:hover { background: #1e40af; }

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
                <!-- Inside-system views invoke the layout screen-switcher script -->
                <li class="menu-item active" onclick="switchDashboardScreen(this, 'main-menu-view')"><a href="#">Main Menu</a></li>
                <li class="menu-item" onclick="switchDashboardScreen(this, 'profile-view')"><a href="#">Profile</a></li>
                <li class="menu-item" onclick="switchDashboardScreen(this, 'marks-view')"><a href="#">Marks</a></li>
                
                <!-- Chats retains regular routing escape logic to your chat index view -->
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
            
            <!-- SCREEN 1: Main Menu View (Visible on Load) -->
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
                    </div>
                    <div class="metric-card" style="width:240px;">
                        <div class="m-title" style="color:#7c3aed;">Recommended Topic</div>
                        <div class="m-val" id="recommended-topic-val" style="font-size:18px; margin-top:10px; margin-bottom:15px;">Loading...</div>
                        <a href="#" class="btn-topic-action">VIEW TOPICS</a>
                    </div>
                </section>
                

                <section class="feed-container">
                    <!-- CREATE DISCUSSION ACTIONS SECTION -->
<section style="
    display:flex;
    gap:20px;
    margin-bottom:30px;
    flex-wrap:wrap;
">

    <!-- CREATE TOPIC CARD -->
    <a href="{{ route('topics.create') }}"
       style="
       background:#2563eb;
       color:white;
       padding:20px;
       border-radius:12px;
       text-decoration:none;
       width:260px;
       display:flex;
       align-items:center;
       gap:15px;
       box-shadow:0 2px 5px rgba(0,0,0,0.1);
       transition:0.2s;
       "
       onmouseover="this.style.background='#1d4ed8'"
       onmouseout="this.style.background='#2563eb'">

        <div style="
            background:white;
            color:#2563eb;
            width:45px;
            height:45px;
            border-radius:50%;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:22px;
        ">
            💬
        </div>

        <div>
            <h3 style="
                font-size:16px;
                font-weight:700;
                margin-bottom:5px;
            ">
                Create Topic
            </h3>

            <p style="
                font-size:12px;
                opacity:0.9;
            ">
                Start a new discussion
            </p>
        </div>

    </a>



    <!-- CREATE GROUP CARD -->
    <a href="{{ route('groups.create') }}"
       style="
       background:#10b981;
       color:white;
       padding:20px;
       border-radius:12px;
       text-decoration:none;
       width:260px;
       display:flex;
       align-items:center;
       gap:15px;
       box-shadow:0 2px5px rgba(0,0,0,0.1);
       transition:0.2s;
       "
       onmouseover="this.style.background='#059669'"
       onmouseout="this.style.background='#10b981'">

        <div style="
            background:white;
            color:#10b981;
            width:45px;
            height:45px;
            border-radius:50%;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:22px;
        ">
            👥
        </div>


        <div>
            <h3 style="
                font-size:16px;
                font-weight:700;
                margin-bottom:5px;
            ">
                Create Group
            </h3>

            <p style="
                font-size:12px;
                opacity:0.9;
            ">
                Create a discussion group
            </p>
        </div>

    </a>


</section>
                    <div class="feed-title">Recent Announcements</div>
                    <div class="feed-list" id="live-announcements-container">
                        <div class="welcome-sub">Fetching latest updates...</div>
                    </div>
                    <button class="btn-view-all" onclick="document.querySelector('[onclick*=\'announcements-view\']').click()">VIEW ALL</button>
                </section>
                
                <div style="background: white; padding: 20px; border-radius: 8px; margin-top: 20px; border: 1px solid #e2e8f0;">
                    <h3 style="color: #1e293b; margin-bottom: 10px;">✍️ Available Assessments</h3>
                    <p style="color: #64748b; font-size: 14px; margin-bottom: 15px;">Your registered course streams have active evaluation windows open.</p>
                    <a href="/quizzes/1" style="display: inline-block; padding: 10px 20px; background: #10b981; color: white; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 14px;">
                        🚀 Attempt Active BIT 2201 Quiz
                    </a>
                </div>
            </div>

            <!-- SCREEN 2: Profile View -->
            <div id="profile-view" class="dashboard-screen">
                <div class="welcome-header">
                    <h1 class="welcome-txt">My Profile</h1>
                    <p class="welcome-sub">Manage your account information and preferences.</p>
                </div>
                <div class="placeholder-card" style="max-width: 100%;">
                    <h2 style="margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">Account Overview</h2>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px;">
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 4px;">Full Name</label>
                            <div style="font-size: 15px; font-weight: 600; color: #0f172a;">{{ Auth::user()->name }}</div>
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 4px;">Email Address</label>
                            <div style="font-size: 15px; font-weight: 600; color: #0f172a;">{{ Auth::user()->email ?? 'student@university.edu' }}</div>
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 4px;">Student Registration No.</label>
                            <div style="font-size: 15px; font-weight: 600; color: #0f172a;">BIT/2026/0842</div>
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 4px;">Current Program</label>
                            <div style="font-size: 15px; font-weight: 600; color: #0f172a;">BSc. Information Technology</div>
                        </div>
                    </div>
                    
                    <h2 style="margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; margin-top: 20px;">Preferences & Settings</h2>
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

            <!-- SCREEN 3: Marks View -->
            <div id="marks-view" class="dashboard-screen">
                <div class="welcome-header">
                    <h1 class="welcome-txt">Academic Standing</h1>
                    <p class="welcome-sub">Track your grading records and continuous assessment progress.</p>
                </div>
                <div class="placeholder-card" style="max-width: 100%;">
                    <h2 style="margin-bottom: 20px;">Course Grades</h2>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
                            <thead>
                                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                    <th style="padding: 12px; color: #64748b; font-weight: 700;">Course Code</th>
                                    <th style="padding: 12px; color: #64748b; font-weight: 700;">Course Title</th>
                                    <th style="padding: 12px; color: #64748b; font-weight: 700; text-align: center;">Coursework (50%)</th>
                                    <th style="padding: 12px; color: #64748b; font-weight: 700; text-align: center;">Exam (50%)</th>
                                    <th style="padding: 12px; color: #64748b; font-weight: 700; text-align: center;">Total</th>
                                    <th style="padding: 12px; color: #64748b; font-weight: 700; text-align: center;">Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 12px; font-weight: 600; color: #0f172a;">BIT 2201</td>
                                    <td style="padding: 12px; color: #334155;">Software Engineering</td>
                                    <td style="padding: 12px; text-align: center; color: #334155;">42 / 50</td>
                                    <td style="padding: 12px; text-align: center; color: #94a3b8;">--</td>
                                    <td style="padding: 12px; text-align: center; font-weight: 600; color: #94a3b8;">--</td>
                                    <td style="padding: 12px; text-align: center;"><span style="background: #eff6ff; color: #2563eb; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: 600;">Active</span></td>
                                </tr>
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 12px; font-weight: 600; color: #0f172a;">BIT 2104</td>
                                    <td style="padding: 12px; color: #334155;">Database Systems</td>
                                    <td style="padding: 12px; text-align: center; color: #334155;">46 / 50</td>
                                    <td style="padding: 12px; text-align: center; color: #334155;">41 / 50</td>
                                    <td style="padding: 12px; text-align: center; font-weight: 600; color: #0f172a;">87%</td>
                                    <td style="padding: 12px; text-align: center; font-weight: 700; color: #16a34a;">A</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 12px; font-weight: 600; color: #0f172a;">MTH 1202</td>
                                    <td style="padding: 12px; color: #334155;">Numerical Analysis</td>
                                    <td style="padding: 12px; text-align: center; color: #334155;">38 / 50</td>
                                    <td style="padding: 12px; text-align: center; color: #334155;">37 / 50</td>
                                    <td style="padding: 12px; text-align: center; font-weight: 600; color: #0f172a;">75%</td>
                                    <td style="padding: 12px; text-align: center; font-weight: 700; color: #2563eb;">B+</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div style="margin-top: 24px; background: #faf5ff; border: 1px solid #e9d5ff; padding: 14px; border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
                        <span style="font-size: 13px; font-weight: 600; color: #6b21a8;">💡 Forum Activity Weighting Balance</span>
                        <span style="font-size: 13px; font-weight: 700; color: #6b21a8;">+10% Bonus Applied</span>
                    </div>
                </div>
            </div>

            <!-- SCREEN 4: Notifications View -->
            <div id="notifications-view" class="dashboard-screen">
                <div class="welcome-header">
                    <h1 class="welcome-txt">Inbox Alerts</h1>
                    <p class="welcome-sub">Stay up to date with activity alerts across your workspace.</p>
                </div>
                <div class="placeholder-card" style="max-width: 100%;">
                    <h2 style="margin-bottom: 20px;">Recent Notifications</h2>
                    <div style="display: flex; flex-direction: column; gap: 14px;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; padding: 12px; border-radius: 8px; background: #f8fafc; border-left: 4px solid #2563eb;">
                            <div>
                                <div style="font-size: 14px; font-weight: 600; color: #1e293b;">New Reply in Database Design</div>
                                <div style="font-size: 13px; color: #64748b; margin-top: 2px;">Dr. Allen replied to your question on 3NF normalization structures.</div>
                            </div>
                            <span style="font-size: 11px; color: #94a3b8; white-space: nowrap;">12 mins ago</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; padding: 12px; border-radius: 8px; background: #f8fafc; border-left: 4px solid #10b981;">
                            <div>
                                <div style="font-size: 14px; font-weight: 600; color: #1e293b;">Assessment Graded</div>
                                <div style="font-size: 13px; color: #64748b; margin-top: 2px;">Your recent submission for Numerical Analysis Assignment 2 has been graded.</div>
                            </div>
                            <span style="font-size: 11px; color: #94a3b8; white-space: nowrap;">2 hours ago</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; padding: 12px; border-radius: 8px; background: #f8fafc; border-left: 4px solid #ef4444;">
                            <div>
                                <div style="font-size: 14px; font-weight: 600; color: #1e293b;">Urgent: Group Sign-up Deadline</div>
                                <div style="font-size: 13px; color: #64748b; margin-top: 2px;">Ensure your Software Engineering group roster (max 5 members) is synchronized via Git.</div>
                            </div>
                            <span style="font-size: 11px; color: #94a3b8; white-space: nowrap;">1 day ago</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SCREEN 5: Announcements View -->
            <div id="announcements-view" class="dashboard-screen">
                <div class="welcome-header">
                    <h1 class="welcome-txt">System Bulletin</h1>
                    <p class="welcome-sub">Official updates from your courses and discussion coordinators.</p>
                </div>
                <div class="placeholder-card" style="max-width: 100%;">
                    <h2 style="margin-bottom: 20px;">All Announcements</h2>
                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <div style="border-bottom: 1px solid #e2e8f0; padding-bottom: 16px;">
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                                <span style="background: #fee2e2; color: #ef4444; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px; text-transform: uppercase;">Department Directive</span>
                                <span style="font-size: 12px; color: #94a3b8;">Posted July 5, 2026</span>
                            </div>
                            <h3 style="font-size: 16px; color: #0f172a; margin-bottom: 6px;">Final Software Design Document (SDD) Review Sessions</h3>
                            <p style="font-size: 14px; color: #64748b; line-height: 1.5;">Coordinators will run live evaluation sessions for all workspace project groups next week. Please confirm your Git repository link is fully operational and configured with clean branches before booking a review slot.</p>
                        </div>
                        
                        <div style="border-bottom: 1px solid #e2e8f0; padding-bottom: 16px;">
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                                <span style="background: #e0f2fe; color: #0284c7; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px; text-transform: uppercase;">Global Broadcast</span>
                                <span style="font-size: 12px; color: #94a3b8;">Posted June 28, 2026</span>
                            </div>
                            <h3 style="font-size: 16px; color: #0f172a; margin-bottom: 6px;">Real-Time Chat Engine Deployment (Laravel Reverb)</h3>
                            <p style="font-size: 14px; color: #64748b; line-height: 1.5;">The system's discussion framework has been upgraded to utilize unified websocket streaming via Laravel Reverb. Instantly view message updates and team collaboration feedback indicators inside your dynamic workspace containers without manual page reloads.</p>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- JavaScript Single Page view toggles and API fetching handles -->
    <script>
        // Tab and Screen management logic
        function switchDashboardScreen(menuElement, screenId) {
            // 1. Swap navigation link active classes
            document.querySelectorAll('.menu-item').forEach(item => {
                item.classList.remove('active');
            });
            menuElement.classList.add('active');

            // 2. Hide all dashboard views, show the requested view
            document.querySelectorAll('.dashboard-screen').forEach(screen => {
                screen.classList.remove('active-view');
            });
            document.getElementById(screenId).classList.add('active-view');
        }

        // Fetch Live Dashboard Data from your Laravel API routes on load
        document.addEventListener("DOMContentLoaded", function() {
            const apiBaseUrl = window.location.origin + '/api';

            // Pull Machine Learning Topic Recommendations
            fetch(`${apiBaseUrl}/chats/topics`)
                .then(res => res.json())
                .then(topics => {
                    const recommendation = topics.find(t => t.recommended === true) || topics[0];
                    if(recommendation) {
                        document.getElementById('recommended-topic-val').innerText = recommendation.title;
                    }
                })
                .catch(err => {
                    document.getElementById('recommended-topic-val').innerText = "Database Design";
                    console.warn("Using layout default topic field value.", err);
                });

            // Pull Live Academic Quiz Announcements
            fetch(`${apiBaseUrl}/academic/announcements`)
                .then(res => res.json())
                .then(announcements => {
                    const container = document.getElementById('live-announcements-container');
                    container.innerHTML = '';

                    announcements.forEach(item => {
                        container.innerHTML += `
                            <div class="feed-item">
                                <div class="feed-avatar">D</div>
                                <div>
                                    <div class="feed-msg-title">${item.message}</div>
                                    <div class="feed-time">Just Now</div>
                                </div>
                            </div>
                        `;
                    });
                })
                .catch(err => {
                    document.getElementById('live-announcements-container').innerHTML = `
                        <div class="feed-item">
                            <div class="feed-avatar">D</div>
                            <div><div class="feed-msg-title">New quiz available: Software Engineering</div><div class="feed-time">2 hours ago</div></div>
                        </div>
                        <div class="feed-item">
                            <div class="feed-avatar">D</div>
                            <div><div class="feed-msg-title">Department meeting on Friday</div><div class="feed-time">1 day ago</div></div>
                        </div>
                    `;
                });
        });
    </script>
</body>
</html>