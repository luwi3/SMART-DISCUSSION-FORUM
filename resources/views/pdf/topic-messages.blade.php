<!DOCTYPE html>
<html>
<head>
    <title>{{ $topic->title }}</title>
    <style>
        body { font-family: sans-serif; color: #333; }
        .header { border-bottom: 2px solid #4A5568; padding-bottom: 10px; margin-bottom: 20px; }
        .topic-title { font-size: 24px; color: #2D3748; margin: 0; }
        .message-box { margin-bottom: 15px; padding: 10px; border-bottom: 1px solid #E2E8F0; }
        .meta { font-size: 12px; color: #718096; margin-bottom: 5px; }
        .content { font-size: 14px; line-height: 1.5; }
    </style>
</head>
<body>

    <div class="header">
        <h1 class="topic-title">{{ $topic->title }}</h1>
        <p style="font-size: 12px; color: #718096;">Exported on: {{ now()->toDayDateTimeString() }}</p>
    </div>

    @foreach($topic->messages as $message)
        <div class="message-box">
            <div class="meta">
                <strong>{{ $message->user->name ?? 'Unknown Member' }}</strong> 
                • {{ $message->created_at->format('M d, Y h:i A') }}
            </div>
            <div class="content">
                {{ $message->content }}
            </div>
        </div>
    @endforeach

</body>
</html>