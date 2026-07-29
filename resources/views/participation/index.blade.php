<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Automated Participation Matrix - Smart Discussion Forum</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background-color: #f4f7fc; padding: 40px; color: #334155; }
        
        .matrix-container { background: white; padding: 32px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03); border: 1px solid #e2e8f0; }
        .matrix-header { margin-bottom: 24px; }
        .matrix-title { color: #0b2265; font-size: 24px; font-weight: 700; }
        .matrix-desc { font-size: 14px; color: #64748b; margin-top: 4px; }
        
        /* Grid Table Styling matching your sketch */
        .table-scroll { width: 100%; overflow-x: auto; }
        .grade-table { width: 100%; border-collapse: collapse; margin-top: 20px; text-align: left; min-width: 500px; }
        .grade-table th, .grade-table td { padding: 14px 18px; border: 1px solid #cbd5e1; font-size: 14px; }
        
        .grade-table th { background-color: #f8fafc; color: #0f172a; font-weight: 700; }
        .main-topic-header { background-color: #eff6ff !important; color: #1e40af !important; text-align: center; font-size: 16px; }
        .sub-topic-header { text-align: center; background-color: #f1f5f9; min-width: 120px; }
        
        .student-name-cell { font-weight: 600; color: #334155; background-color: #ffffff; }
        .score-cell { text-align: center; font-weight: 700; color: #0b2265; }
        .no-data { color: #94a3b8; font-style: italic; text-align: center; padding: 20px; }
        
        .back-btn { display: inline-block; margin-bottom: 20px; color: #2563eb; text-decoration: none; font-weight: 600; font-size: 14px; }
        .back-btn:hover { text-decoration: underline; }

        @media (max-width: 640px) {
            body { padding: 20px 14px; }
            .matrix-container { padding: 18px; }
        }
    </style>
</head>
<body>

    <a href="{{ route('lecturer.dashboard') }}" class="back-btn">← Back to Dashboard</a>

    <div class="matrix-container">
        <div class="matrix-header">
            <h2 class="matrix-title">📊 Student Participation Ledger</h2>
            <p class="matrix-desc">System-automated marks calculated out of 20 based on student forum replies per topic.</p>
        </div>

        <div class="table-scroll">
        <table class="grade-table">
            <thead>
                <tr>
                    <th rowspan="2" style="vertical-align: middle; width: 30%;">Student Name</th>
                    <th colspan="{{ $topics->count() > 0 ? $topics->count() : 1 }}" class="main-topic-header">Topics</th>
                </tr>
                <tr>
                    @if($topics->count() > 0)
                        @foreach($topics as $topic)
                            <th class="sub-topic-header">{{ $topic->title }}</th>
                        @endforeach
                    @else
                        <th class="no-data">No topics created yet</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @if($students->count() > 0)
                    @foreach($students as $student)
                        <tr>
                            <td class="student-name-cell">👤 {{ $student->name }}</td>
                            
                            @if($topics->count() > 0)
                                @foreach($topics as $topic)
                                    <td class="score-cell">
                                        @if(isset($matrix[$student->id][$topic->id]))
                                            {{ $matrix[$student->id][$topic->id] }}/20
                                        @else
                                            0/20
                                        @endif
                                    </td>
                                @endforeach
                            @else
                                <td class="no-data">--</td>
                            @endif
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="{{ $topics->count() + 1 }}" class="no-data">No students registered in the system.</td>
                    </tr>
                @endif
            </tbody>
        </table>
        </div>
    </div>

</body>
</html>