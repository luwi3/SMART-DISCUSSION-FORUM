<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Lecturer - Smart Discussion Forum</title>
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
        .dashboard-body { padding: 30px; max-width: 800px; width: 100%; margin: 0 auto; }
        .form-card { background: white; border-radius: 12px; padding: 30px; border: 1px solid #eef2f6; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
        .form-title { font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 24px; }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 8px; }
        .form-input { width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; color: #0f172a; }
        .form-input:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
        .submit-btn { background-color: #2563eb; color: white; border: none; padding: 12px 24px; font-size: 14px; font-weight: 700; border-radius: 6px; cursor: pointer; transition: background 0.2s; }
        .submit-btn:hover { background-color: #1d4ed8; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-brand">
            <svg class="brand-icon" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>
            <div class="brand-text">SMART DISCUSSION FORUM</div>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="menu-item active"><a href="#">Register Lecturer</a></li>
            <!-- <li class="menu-item"><a href="#">Profile</a></li> -->
            <!-- <li class="menu-item"><a href="#">Statistics of Users</a></li> -->
            <!-- <li class="menu-item"><a href="#">Groups</a></li> -->
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
        <nav class="top-navbar"><div class="nav-title">SMART DISCUSSION FORUM</div></nav>
        <div class="dashboard-body">
            <div class="form-card">
                <h2 class="form-title">Register New Lecturer 🧑‍🏫</h2>
                
           <form method="POST" action="{{ route('admin.lecturers.store') }}">
            @if ($errors->any())
    <div style="color: red; margin-bottom: 15px;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-input" required placeholder="e.g. Dr. Jane Smith">
                    </div>
                      <div class="form-group">
                        <label class="form-label">User Name</label>
                        <input type="text" name="username" class="form-input" required placeholder="e.g. LecturerJane">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-input" required placeholder="e.g. janesmith@university.edu">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-input" required placeholder="••••••••">
                    </div>

                     <div class="form-group">
                        <label class="form-label">Staff ID</label>
                        <input type="text" name="staffNo" class="form-input" required placeholder="e.g. LECT001">
                    </div>
                     <div class="form-group">
                        <label class="form-label">Department</label>
                        <input type="text" name="department" class="form-input" required placeholder="e.g. Computer Science">
                    </div>
                    
                    <button type="submit" class="submit-btn">Register Lecturer</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>