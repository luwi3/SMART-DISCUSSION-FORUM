<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gradebook - {{ $quiz->title }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background-color: #f8fafc; padding: 40px; display: flex; justify-content: center; }
        .gradebook-container { max-width: 1000px; width: 100%; background: white; border-radius: 12px; border: 1px solid #e2e8f0; padding: 32px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 24px; margin-bottom: 24px; }
        .quiz-info h1 { font-size: 22px; color: #0c2340; font-weight: 700; }
        .quiz-info p { font-size: 14px; color: #64748b; margin-top: 4px; }
        .stats-badge { background: #eff6ff; color: #2563eb; padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 700; border: 1px solid #bfdbfe; }
        
        /* Table Layout Styling */
        table { width: 100%; border-collapse: collapse; text-align: left; margin-top: 10px; }
        th { background-color: #f8fafc; color: #475569; font-size: 13px; font-weight: 700; padding: 14px 18px; border-bottom: 2px solid #e2e8f0; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 16px 18px; border-bottom: 1px solid #e2e8f0; font-size: 14px; color: #334155; }
        tr:hover { background-color: #f8fafc; }
        
        .status { font-weight: 700; font-size: 12px; padding: 4px 8px; border-radius: 4px; }
        .status-auto { background: #fef3c7; color: #d97706; }
        .status-normal { background: #dcfce7; color: #16a34a; }
        .score-display { font-weight: 700; color: #0c2340; }
    </style>
</head>
<body>

<div class="gradebook-container">
    <div class="header">
        <div class="quiz-info">
            <h1>Quiz Assessment Gradebook</h1>
            <p>Course Code: <strong>{{ $quiz->courseCode }}</strong> | Title: {{ $quiz->title }}</p>
        </div>
        <div class="stats-badge">
            Total Submissions: {{ $submissions->count() }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Registration No.</th>
                <th>Student Name</th>
                <th>Time Submitted</th>
                <th>Submission Mode</th>
                <th>Marks Secured</th>
            </tr>
        </thead>
        <tbody>
            @forelse($submissions as $submission)
                <tr>
                    <td><strong>{{ $submission->regNo }}</strong></td>
                    <td>{{ $submission->student_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($submission->timeSubmitted)->format('d M Y, h:i A') }}</td>
                    <td>
                        @if($submission->autoSubmit)
                            <span class="status status-auto">⏰ Timed Out</span>
                        @else
                            <span class="status status-normal">✅ Submitted</span>
                        @endif
                    </td>
                    <td><span class="score-display">{{ $submission->marks }} Marks</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #94a3b8; padding: 40px; font-weight: 500;">No students have submitted this assessment yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

</body>
</html>