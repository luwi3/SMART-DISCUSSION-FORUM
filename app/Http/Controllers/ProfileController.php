<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // If it's an admin, safely return the user without lazy-loading non-existent profile relationships
        if ($user->role === 'administrator' || $user->role === 'admin') {
            return view('profile.edit', [
                'user' => $user,
            ]);
        }

        // For students and lecturers, load their specific side-table models securely
        return view('profile.edit', [
            'user' => $user->load(['student', 'lecturer']),
        ]);
    }

    /**
     * Update the user's profile information dynamically based on role.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // 1. BASELINE VALIDATION: Standard fields that everyone shares
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ];

        // 2. DYNAMIC VALIDATION: Add rules depending on who is saving changes
        if ($user->role === 'student') {
            $rules['phone'] = ['nullable', 'string', 'max:20'];
            $rules['studentCategory'] = ['nullable', 'string', 'max:50'];
        } elseif ($user->role === 'lecturer') {
            $rules['department'] = ['nullable', 'string', 'max:100'];
        }

        // Validate the incoming data
        $validated = $request->validate($rules);

        // 3. SAVE CORE USER DATA
        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // 4. SAVE RELATION-SPECIFIC DATA
        if ($user->role === 'student' && $user->student) {
            // Updates fields directly in your 'students' table
            $user->student->update([
                'phone' => $request->input('phone'),
                'student_category' => $request->input('studentCategory'),
            ]);
        } elseif ($user->role === 'lecturer' && $user->lecturer) {
            // Updates fields directly in your 'lecturers' table
            $user->lecturer->update([
                'department' => $request->input('department'),
            ]);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}