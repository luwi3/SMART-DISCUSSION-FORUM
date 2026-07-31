<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\User;
use App\Models\Message;
use App\Models\Lecturer;
use Illuminate\Support\Facades\Mail;
use App\Mail\WarningNotice;
use Illuminate\Support\Facades\DB;

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
            // 🟢 Changed from ->send() to ->queue() to prevent 504 Gateway Timeouts
            Mail::to($student->user->email)->queue(new WarningNotice($student));
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

        /*
        |--------------------------------------------------------------------------
        | Category drill-down lists — only fetched when the admin actually clicks
        | into that view, so the default dashboard load stays lightweight.
        |--------------------------------------------------------------------------
        */
        $view = $request->query('view', 'dashboard');

        $suspendedStudents = collect();
        $activeStudentsList = collect();
        $warningStudentsList = collect();
        $allUsersList = collect();
        $lecturers = collect();
        $courseActivity = collect();

        if ($view === 'blacklist') {
            $suspendedStudents = Student::where('status', 'blacklisted')->with('user')->get();
        } elseif ($view === 'active') {
            $activeStudentsList = Student::where('status', 'active')->with('user')->get();
        } elseif ($view === 'warning') {
            $warningStudentsList = Student::where('status', 'warning')->with('user')->get();
        } elseif ($view === 'lecturers') {
            $lecturers = Lecturer::with('user')
                ->get()
                ->sortBy(fn ($lecturer) => $lecturer->user->name ?? '')
                ->values();
        } elseif ($view === 'all') {
            $allUsersList = User::orderBy('name')->get();
        } elseif ($view === 'courses') {
            // Every student with a courseCode, left-joined against their total
            // message count across the whole forum (general chat + groups + topics).
            // LEFT JOIN keeps students with zero messages visible too — that gap
            // is useful information, not noise.
            $rows = DB::table('students')
                ->join('users', 'users.id', '=', 'students.user_id')
                ->leftJoin('messages', 'messages.user_id', '=', 'students.user_id')
                ->select(
                    'students.courseCode',
                    'students.regNo',
                    'users.name',
                    DB::raw('COUNT(messages.id) as message_count')
                )
                ->whereNotNull('students.courseCode')
                ->groupBy('students.courseCode', 'students.regNo', 'users.name')
                ->orderBy('students.courseCode')
                ->orderByDesc('message_count')
                ->get();

            // Group into courseCode => ranked list of students, and attach a
            // small summary (total messages, student count, average) per course.
            $courseActivity = $rows->groupBy('courseCode')->map(function ($students, $courseCode) {
                $totalMessages = $students->sum('message_count');
                $studentCount = $students->count();

                return [
                    'students' => $students->values(),
                    'total_messages' => $totalMessages,
                    'student_count' => $studentCount,
                    'average' => $studentCount > 0 ? round($totalMessages / $studentCount, 1) : 0,
                ];
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Peak usage statistics — when is the forum busiest?
        |--------------------------------------------------------------------------
        */
        $hourlyActivityRaw = Message::selectRaw('HOUR(created_at) as hour, COUNT(*) as total')
            ->groupBy('hour')
            ->pluck('total', 'hour');

        $hourlyActivity = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $hourlyActivity[$hour] = (int) ($hourlyActivityRaw[$hour] ?? 0);
        }

        $totalMessages = array_sum($hourlyActivity);
        $peakHour = $totalMessages > 0 ? array_search(max($hourlyActivity), $hourlyActivity) : null;
        $peakHourCount = $peakHour !== null ? $hourlyActivity[$peakHour] : 0;

        $daysOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        $dailyActivityRaw = Message::selectRaw('DAYNAME(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $dailyActivity = [];
        foreach ($daysOrder as $day) {
            $dailyActivity[$day] = (int) ($dailyActivityRaw[$day] ?? 0);
        }

        $peakDay = $totalMessages > 0 ? array_search(max($dailyActivity), $dailyActivity) : null;
        $peakDayCount = $peakDay !== null ? $dailyActivity[$peakDay] : 0;

        // 🟢 eager-load 'user' so the JSON response includes student names
        $data = compact(
            'totalUsers',
            'activeStudents',
            'warningList',
            'blacklistedUsers',
            'suspendedStudents',
            'activeStudentsList',
            'warningStudentsList',
            'allUsersList',
            'lecturers',
            'courseActivity',
            'activePercentage',
            'warningPercentage',
            'blacklistedPercentage',
            'hourlyActivity',
            'dailyActivity',
            'totalMessages',
            'peakHour',
            'peakHourCount',
            'peakDay',
            'peakDayCount'
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

    /**
     * 🟢 NEW: Remove a lecturer. Deletes both the Lecturer profile
     * (staffNo, department) and its linked User login account together,
     * so we never leave an orphaned login with no staff profile, or a
     * dangling profile pointing at a deleted user.
     */
    public function removeLecturer(Request $request, $staffNo)
    {
        $lecturer = Lecturer::where('staffNo', $staffNo)->firstOrFail();
        $linkedUser = $lecturer->user;

        $lecturer->delete();

        if ($linkedUser) {
            $linkedUser->delete();
        }

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success']);
        }

        return redirect()->route('admin.dashboard', ['view' => 'lecturers'])
            ->with('success', 'Lecturer account removed successfully.');
    }
}