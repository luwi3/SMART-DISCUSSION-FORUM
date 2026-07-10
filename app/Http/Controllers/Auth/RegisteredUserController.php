<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Lecturer; // 🔍 Imported your Lecturer Model
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 👇 STEP 2 DEBUG FORCING: This halts execution to display exactly what the form sent!
        dd($request->all());

        // 1. Validate both user and conditional profile inputs
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'username' => ['required', 'string', 'max:255', 'unique:'.User::class], 
            'phone' => ['required', 'string', 'max:20'], 
            'role' => ['required', 'string', 'in:student,lecturer'], // 🔍 Added role selection validation
            'course_code' => ['required_if:role,student', 'nullable', 'string', 'max:50'], // Required only if student
            'reg_no' => ['required_if:role,student', 'nullable', 'string', 'max:50'],      // Required only if student
            'agreed_to_rules' => ['required', 'accepted'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. Create the main user account with the dynamic role from the form selection
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'phone' => $request->phone,
            'role' => $request->role, // 🔍 No longer hardcoded as 'student'!
            'agreed_to_rules' => (bool) $request->agreed_to_rules,
            'password' => Hash::make($request->password),
            'status' => 'active',
        ]);

        // 3. Conditional profile router splits users into their matching table profile
        if ($request->role === 'lecturer') {
            // Automatically build a clean profile row inside the lecturers table
            Lecturer::create([
                'user_id' => $user->id,
                'staffNo' => 'LEC-' . date('Y') . '-' . strtoupper(Str::random(4)), // Generates automatic staff number
                'department' => 'Faculty of Computing',                             // Default assignment fallback
            ]);
        } else {
            // Otherwise, populate the student tracking table
            Student::create([
                'user_id' => $user->id,
                'regNo' => $request->reg_no,          
                'courseCode' => $request->course_code,  
                'status' => 'active',
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}