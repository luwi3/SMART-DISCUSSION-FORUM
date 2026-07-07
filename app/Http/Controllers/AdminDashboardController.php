<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;

class AdminDashboardController extends Controller
{
    public function index()
{
    // 1. Automatically blacklist students inactive for more than 5 minutes
    $thresholdTime = now()->subMinutes(5);
    
    \App\Models\Student::where('status', 'active')
        ->where('lastCommDate', '<', $thresholdTime)
        ->update(['status' => 'blacklisted']);

    // 2. Fetch the updated statistics from the database
    $totalUsers = \App\Models\User::count();
    $activeStudents = \App\Models\Student::where('status', 'active')->count();
    $blacklistedUsers = \App\Models\Student::where('status', 'blacklisted')->count();
    
    // Leaving this as 0 for now until we define warning rules
    $warningList = 0; 
  $suspendedStudents = Student::where('status', 'blacklisted')->get();
    // 3. Pass variables to the view
    return view('dashboards.admin', compact(
        'totalUsers', 
        'activeStudents', 
        'warningList', 
        'blacklistedUsers',
        'suspendedStudents'
    ));
}
}