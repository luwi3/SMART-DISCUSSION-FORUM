<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Create Group - Smart Discussion Forum</title>


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
font-size:13px;
font-weight:700;
color:#334155;
margin-bottom:6px;

}


input,textarea{

width:100%;
padding:12px;
border:1px solid #cbd5e1;
border-radius:8px;
margin-bottom:20px;
font-size:14px;
outline:none;

}


textarea{

height:120px;
resize:none;

}


input:focus,textarea:focus{

border-color:#10b981;

}


button{

width:100%;
padding:12px;
background:#10b981;
color:white;
border:none;
border-radius:8px;
font-weight:700;
cursor:pointer;

}


button:hover{

background:#059669;

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


<h1>👥 Create Group</h1>


<p>
Create a study group where members can discuss and collaborate.
</p>



<form method="POST" action="#">

@csrf


<label>
Group Name
</label>


<input 
type="text"
name="name"
placeholder="Example: Software Engineering Team">



<label>
Group Description
</label>


<textarea
name="description"
placeholder="Describe the purpose of this group"></textarea>



<button>
Create Group
</button>


</form>



<a class="back" href="{{ route('student.dashboard') }}">
← Back to Dashboard
</a>


</div>


</body>

</html>