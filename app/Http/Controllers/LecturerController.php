<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lecturer; 
use App\Models\User; // 👤 Imported main user model
use Illuminate\Support\Facades\Hash; 

class LecturerController extends Controller
{
    public function create()
    {
        return view('dashboards.register-lecturer');
    }

  public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'username' => 'required|string|max:255|unique:users',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
        'staffNo' => 'required|string|unique:lecturers,staffNo',
        'department' => 'required|string|max:255',
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'username' => $validated['username'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role' => 'lecturer',
    ]);

    Lecturer::create([
        'staffNo' => $validated['staffNo'],
        'user_id' => $user->id,
        'department' => $validated['department'],
    ]);

    if ($request->wantsJson()) {
        return response()->json(['status' => 'success']);
    }

    return redirect()->route('admin.lecturers.create')->with('success', 'Lecturer registered successfully!');
}
}