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
        // 1. Validate all incoming form fields
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'staffNo' => 'required|string|unique:lecturers,staffNo',
            'department' => 'required|string|max:255',
        ]);

        // 2. Step One: Create the User login account
        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // 3. Step Two: Create the Lecturer record linked to that User
        Lecturer::create([
            'staffNo' => $validated['staffNo'],
            'user_id' => $user->id, // 🔗 Links the lecturer to the newly created user ID
            'department' => $validated['department'],
        ]);

        // 4. Redirect back with a success message
        return redirect()->route('admin.lecturers.create')->with('success', 'Lecturer registered successfully!');
    }
}