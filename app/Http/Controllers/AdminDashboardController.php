<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Define our time thresholds 🗓️
        $warningThreshold = now()->subDays(2);
        $blacklistThreshold = now()->subDays(3);
        
        // 2. Automatically transition active students to warning after 2 days of inactivity ⚠️
        Student::where('status', 'active')
            ->where('lastCommDate', '<', $warningThreshold)
            ->update(['status' => 'warning']);

        // 3. Automatically transition warning students to blacklisted after 3 days of inactivity 🛑
        Student::where('status', 'warning')
            ->where('lastCommDate', '<', $blacklistThreshold)
            ->update(['status' => 'blacklisted']);

        // 4. Fetch the updated statistics from the database 📊
        $totalUsers = User::count();
        $activeStudents = Student::where('status', 'active')->count();
        $warningList = Student::where('status', 'warning')->count();
        $blacklistedUsers = Student::where('status', 'blacklisted')->count();
        
        // 5. Calculate percentages 📈
        $activePercentage = ($totalUsers > 0) ? ($activeStudents / $totalUsers) * 100 : 0;
        $warningPercentage = ($totalUsers > 0) ? ($warningList / $totalUsers) * 100 : 0;
        $blacklistedPercentage = ($totalUsers > 0) ? ($blacklistedUsers / $totalUsers) * 100 : 0;
        
        // 6. Fetch the collection of blacklisted students for the dashboard table 🗂️
        $suspendedStudents = Student::where('status', 'blacklisted')->get();
        
        // 7. Pass variables to the view ↩️
        return view('dashboards.admin', compact(
            'totalUsers', 
            'activeStudents', 
            'warningList', 
            'blacklistedUsers',
            'suspendedStudents',
            'activePercentage',
            'warningPercentage',
            'blacklistedPercentage'
        ));
    }
}