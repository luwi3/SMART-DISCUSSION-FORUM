<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\Lecturer;
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
        $lecturer = Lecturer::where('user_id', $userId)->first();
        $staffNo = $lecturer ? $lecturer->staffNo : 'STAFF-TEST-01';

        // Retrieve resources belonging to this lecturer
        $resources = Resource::where('staffNo', $staffNo)->orderBy('created_at', 'desc')->get();

        return view('resources.index', compact('resources'));
    }

    /**
     * Process and securely upload a new learning resource file.
     */
    public function store(Request $request)
    {
        $userId = Auth::id();
        $lecturer = Lecturer::where('user_id', $userId)->first();
        $staffNo = $lecturer ? $lecturer->staffNo : 'STAFF-TEST-01';

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'courseCode' => 'required|string|max:50',
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,zip,png,jpg,jpeg|max:20480', // Max 20MB
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            // Store file securely in the 'public/resources' directory
            $path = $file->store('resources', 'public');

            Resource::create([
                'staffNo' => $staffNo,
                'courseCode' => strtoupper($validated['courseCode']),
                'title' => $validated['title'],
                'file_path' => $path,
                'file_type' => $file->getClientOriginalExtension(),
            ]);

            return redirect()->back()->with('success', 'Learning resource uploaded successfully!');
        }

        return redirect()->back()->with('error', 'File upload failed.');
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