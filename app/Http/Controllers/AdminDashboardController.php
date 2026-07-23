<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\WarningNotice;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $warningThreshold = now()->subDays(2);
        $blacklistThreshold = now()->subDays(3);

        $studentsToWarn = Student::where('status', 'active')
            ->where('lastCommDate', '<', $warningThreshold)
            ->get();

        foreach ($studentsToWarn as $student) {
            Mail::to($student->user->email)->send(new WarningNotice($student));
            $student->update(['status' => 'warning']);
        }

        Student::where('status', 'warning')
            ->where('lastCommDate', '<', $blacklistThreshold)
            ->update(['status' => 'blacklisted']);

        $totalUsers = User::count();
        $activeStudents = Student::where('status', 'active')->count();
        $warningList = Student::where('status', 'warning')->count();
        $blacklistedUsers = Student::where('status', 'blacklisted')->count();

        $activePercentage = ($totalUsers > 0) ? ($activeStudents / $totalUsers) * 100 : 0;
        $warningPercentage = ($totalUsers > 0) ? ($warningList / $totalUsers) * 100 : 0;
        $blacklistedPercentage = ($totalUsers > 0) ? ($blacklistedUsers / $totalUsers) * 100 : 0;

        // 🟢 NEW: eager-load 'user' so the JSON response includes student names
        $suspendedStudents = Student::where('status', 'blacklisted')->with('user')->get();

        $data = compact(
            'totalUsers',
            'activeStudents',
            'warningList',
            'blacklistedUsers',
            'suspendedStudents',
            'activePercentage',
            'warningPercentage',
            'blacklistedPercentage'
        );

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return view('dashboards.admin', $data);
    }

    /**
     * 🟢 NEW: JSON-friendly version of the inline activate-student closure
     * currently sitting in routes/web.php, so the Java client has a real
     * endpoint to call instead of an anonymous route function.
     */
    public function activateStudent(Request $request, $regNo)
    {
        $student = Student::where('regNo', $regNo)->firstOrFail();
        $student->status = 'active';
        $student->lastCommDate = now();
        $student->save();

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success']);
        }

        return back()->with('success', 'Student status has been reset to active successfully!');
    }
}