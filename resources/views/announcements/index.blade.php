<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Announcements - Smart Discussion Forum</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background-color: #f8fafc; color: #0f172a; padding: 40px; }
        .container { max-width: 800px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .back-btn { background: #2563eb; color: white; padding: 10px 16px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 14px; }
        .announcement-card { background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        .announcement-title { font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 6px; }
        .announcement-meta { font-size: 12px; color: #64748b; margin-bottom: 12px; font-weight: 600; }
        .announcement-body { font-size: 14px; color: #334155; line-height: 1.5; white-space: pre-line; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📢 All Announcements</h1>
            <a href="{{ route('student.dashboard') }}" class="back-btn">Back to Dashboard</a>
        </div>

        @if(isset($announcements) && count($announcements) > 0)
            @foreach($announcements as $announcement)
                <div class="announcement-card">
                    <div class="announcement-title">{{ $announcement->title }}</div>
                    <div class="announcement-meta">Course: {{ $announcement->courseCode }} • Posted: {{ $announcement->created_y ?? $announcement->created_at->diffForHumans() }}</div>
                    <div class="announcement-body">{{ $announcement->message }}</div>
                </div>
            @endforeach
        @else
            <p style="color: #64748b;">No announcements found.</p>
        @endif
    </div>
</body>
</html>