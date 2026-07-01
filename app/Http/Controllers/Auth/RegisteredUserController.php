<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
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
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
       // 1. Business Validation Rules matching your UI fields
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        'contact' => ['required', 'string', 'max:20'], // Captures your contact input
        'password' => ['required', 'confirmed', \Illuminate\Validation::Rules\Password::defaults()],
        'role' => ['required', 'in:Administrator,Lecturer,Student'], // Validates the dropdown selection
        'rules_agreed' => ['required', 'accepted'], // Stops the user if "I have read and understood the rules" is unchecked
    ]);

    // 2. Creating the user record in the database
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'contact' => $request->contact,
        'password' => \Illuminate\Support\Facades\Hash::make($request->password), // Encrypts the password securely
        'role' => $request->role,
        'status' => 'Active', // Sets default user account status
    ]);

    // 3. Authenticate and log the user in immediately
    event(new \Illuminate\Auth\Events\Registered($user));
    \Illuminate\Support\Facades\Auth::login($user);

    // 4. Redirect them to the dashboard area
    return redirect(route('dashboard', absolute: false));
}
