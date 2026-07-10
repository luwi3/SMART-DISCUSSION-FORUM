<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

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
        // 1. Validate user and student profile inputs 📝
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'username' => ['required', 'string', 'max:255', 'unique:'.User::class], 
            'phone' => ['required', 'string', 'max:20'], 
            'course_code' => ['required', 'string', 'max:50'], 
            'reg_no' => ['required', 'string', 'max:50'],      
            'agreed_to_rules' => ['required', 'accepted'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. Create the main user account defaulting to 'student' 🎓
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'phone' => $request->phone,
            'role' => 'student', 
            'agreed_to_rules' => (bool) $request->agreed_to_rules,
            'password' => Hash::make($request->password),
            'status' => 'active',
        ]);

        // 3. Populate the student tracking table directly 📊
        Student::create([
            'user_id' => $user->id,
            'regNo' => $request->reg_no,          
            'courseCode' => $request->course_code,  
            'status' => 'active',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}