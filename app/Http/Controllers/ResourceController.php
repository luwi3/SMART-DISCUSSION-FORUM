<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\Lecturer;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    /**
     * Display a listing of resources uploaded by the lecturer along with the upload form.
     */
    public function index()
    {
        $userId = Auth::id();

        // Dynamically find or generate a lecturer record for the authenticated user
        $lecturer = Lecturer::firstOrCreate(
            ['user_id' => $userId],
            [
                'staffNo' => 'STAFF-' . strtoupper(uniqid()),
                'name' => Auth::user()->name ?? 'Lecturer'
            ]
        );
        $staffNo = $lecturer->staffNo;

        // Retrieve resources belonging to this specific lecturer
        $resources = Resource::where('staffNo', $staffNo)->orderBy('created_at', 'desc')->get();

        return view('resources.index', compact('resources'));
    }

    /**
     * Process and securely upload a new learning resource file.
     */
    public function store(Request $request)
    {
        $userId = Auth::id();

        // Dynamically find or generate a lecturer record for the authenticated user
        $lecturer = Lecturer::firstOrCreate(
            ['user_id' => $userId],
            [
                'staffNo' => 'STAFF-' . strtoupper(uniqid()),
                'name' => Auth::user()->name ?? 'Lecturer'
            ]
        );
        $staffNo = $lecturer->staffNo;

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'courseCode' => 'required|string|max:50',
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,zip,png,jpg,jpeg|max:20480', // Max 20MB
        ]);

        $file = $request->file('file');
        // Store file securely in the 'public/resources' directory
        $path = $file->store('resources', 'public');

        // 🔧 FIX: trim(strtoupper()) instead of strtoupper() alone. This is the same
        // courseCode-normalization issue as the quiz bug — Quiz/Student comparisons
        // elsewhere in the app use trim(strtoupper()), so a resource/announcement saved
        // with only strtoupper() (still carrying a stray leading/trailing space) would
        // silently never match a student's normalized courseCode when displayed.
        $courseCode = trim(strtoupper($validated['courseCode']));

        Resource::create([
            'staffNo' => $staffNo,
            'courseCode' => $courseCode,
            'title' => $validated['title'],
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
        ]);

        // Automatically create a student announcement with the download link
        Announcement::create([
            'title' => 'New Resource: ' . $validated['title'],
            'courseCode' => $courseCode,
            'message' => "A new learning resource has been published for your course.<br><a href='" . asset('storage/' . $path) . "' class='btn btn-sm btn-primary mt-2' download>Download " . htmlspecialchars($validated['title']) . "</a>",
        ]);

        return redirect()->back()->with('success', 'Learning resource uploaded and broadcasted as an announcement successfully!');
    }

    /**
     * Remove the resource file from storage and delete the database entry.
     */
    public function destroy($id)
    {
        $resource = Resource::findOrFail($id);

        // Delete raw file from local app storage disk
        if (Storage::disk('public')->exists($resource->file_path)) {
            Storage::disk('public')->delete($resource->file_path);
        }

        $resource->delete();

        return redirect()->back()->with('success', 'Resource permanently deleted.');
    }
}