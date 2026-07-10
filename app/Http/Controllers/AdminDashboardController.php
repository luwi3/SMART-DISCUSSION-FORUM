<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\WarningNotice;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Define our time thresholds 🗓️
        $warningThreshold = now()->subDays(2);
        $blacklistThreshold = now()->subDays(3);
        
        // 2. Find active students who have been inactive for more than 2 days 🔍
        $studentsToWarn = Student::where('status', 'active')
            ->where('lastCommDate', '<', $warningThreshold)
            ->get();

        // 3. Loop through each student, send the warning email, and update status ✉️
        foreach ($studentsToWarn as $student) {
            // 🔗 We use ->user->email to look into the users table!
            Mail::to($student->user->email)->send(new WarningNotice($student));
            
            $student->update(['status' => 'warning']);
        }       

        // 4. Automatically transition warning students to blacklisted after 3 days 🛑
        Student::where('status', 'warning')
            ->where('lastCommDate', '<', $blacklistThreshold)
            ->update(['status' => 'blacklisted']);

        // 5. Fetch the updated statistics from the database 📊
        $totalUsers = User::count();
        $activeStudents = Student::where('status', 'active')->count();
        $warningList = Student::where('status', 'warning')->count();
        $blacklistedUsers = Student::where('status', 'blacklisted')->count();
        
        // 6. Calculate percentages 📈
        $activePercentage = ($totalUsers > 0) ? ($activeStudents / $totalUsers) * 100 : 0;
        $warningPercentage = ($totalUsers > 0) ? ($warningList / $totalUsers) * 100 : 0;
        $blacklistedPercentage = ($totalUsers > 0) ? ($blacklistedUsers / $totalUsers) * 100 : 0;
        
        // 7. Fetch the collection of blacklisted students for the dashboard table 🗂️
        $suspendedStudents = Student::where('status', 'blacklisted')->get();
        
        // 8. Pass variables to the view ↩️
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