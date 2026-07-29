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
public function index(Request $request)
{
    $userId = Auth::id();

    $lecturer = Lecturer::firstOrCreate(
        ['user_id' => $userId],
        ['staffNo' => 'STAFF-' . strtoupper(uniqid()), 'name' => Auth::user()->name ?? 'Lecturer']
    );
    $staffNo = $lecturer->staffNo;

    $resources = Resource::where('staffNo', $staffNo)->orderBy('created_at', 'desc')->get();

    if ($request->wantsJson()) {
        return response()->json(compact('resources'));
    }

    return view('resources.index', compact('resources'));
}

public function store(Request $request)
{
    $userId = Auth::id();

    $lecturer = Lecturer::firstOrCreate(
        ['user_id' => $userId],
        ['staffNo' => 'STAFF-' . strtoupper(uniqid()), 'name' => Auth::user()->name ?? 'Lecturer']
    );
    $staffNo = $lecturer->staffNo;

    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'courseCode' => 'required|string|max:50',
        'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,zip,png,jpg,jpeg|max:20480',
    ]);

    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $path = $file->store('resources', 'public');

        $resource = Resource::create([
            'staffNo' => $staffNo,
            'courseCode' => strtoupper($validated['courseCode']),
            'title' => $validated['title'],
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
        ]);

        Announcement::create([
            'title' => 'New Resource: ' . $validated['title'],
            'courseCode' => strtoupper($validated['courseCode']),
            'message' => "A new learning resource has been published for your course.<br><a href='" . asset('storage/' . $path) . "' class='btn btn-sm btn-primary mt-2' download>Download " . htmlspecialchars($validated['title']) . "</a>",
        ]);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'resource' => $resource]);
        }

        return redirect()->back()->with('success', 'Learning resource uploaded and broadcasted as an announcement successfully!');
    }

    if ($request->wantsJson()) {
        return response()->json(['error' => 'File upload failed.'], 422);
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