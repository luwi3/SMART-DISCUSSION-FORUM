<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Topic - Smart Discussion Forum</title>

    <style>
        *{
            box-sizing:border-box;
            margin:0;
            padding:0;
            font-family:'Segoe UI',sans-serif;
        }

        body{
            background:#f8fafc;
            min-height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
        }

        .container{
            background:white;
            width:500px;
            padding:35px;
            border-radius:16px;
            box-shadow:0 5px 20px rgba(0,0,0,0.08);
            border:1px solid #e2e8f0;
        }

        h1{
            color:#0f172a;
            margin-bottom:8px;
        }

        p{
            color:#64748b;
            font-size:14px;
            margin-bottom:25px;
        }

        label{
            display:block;
            color:#334155;
            font-size:13px;
            font-weight:700;
            margin-bottom:6px;
        }

        input, textarea{
            width:100%;
            padding:12px;
            border:1px solid #cbd5e1;
            border-radius:8px;
            margin-bottom:20px;
            outline:none;
            font-size:14px;
        }

        textarea{
            height:120px;
            resize:none;
        }

        input:focus, textarea:focus{
            border-color:#2563eb;
        }

        button{
            width:100%;
            padding:12px;
            border:none;
            background:#2563eb;
            color:white;
            border-radius:8px;
            font-weight:700;
            cursor:pointer;
        }

        button:hover{
            background:#1d4ed8;
        }

        .back{
            display:block;
            text-align:center;
            margin-top:15px;
            color:#64748b;
            text-decoration:none;
            font-size:14px;
        }

    </style>

</head>

<body>

<div class="container">

    <h1>💬 Create Topic</h1>

    <p>
        Start a new discussion and allow members to participate.
    </p>


    <form method="POST" action="{{ route('topics.store') }}">
    @csrf

    <input type="text" name="title">

    <textarea name="description"></textarea>

    <button type="submit">
        Create Topic
    </button>

</form>

    <a class="back" href="{{ route('student.dashboard') }}">
        ← Back to Dashboard
    </a>

</div>


</body>
</html>