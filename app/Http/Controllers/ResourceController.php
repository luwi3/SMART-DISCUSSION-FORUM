<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\Lecturer;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Exception;

class ResourceController extends Controller
{
    /**
     * Display a listing of resources uploaded by the lecturer along with the upload form.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $lecturer = Lecturer::where('user_id', $userId)->firstOrFail();
        $staffNo = $lecturer->staffNo;

        $resources = Resource::where('staffNo', $staffNo)->orderBy('created_at', 'desc')->get();

        if ($request->wantsJson()) {
            return response()->json(compact('resources'));
        }

        return view('resources.index', compact('resources'));
    }

    /**
     * Process and securely upload a new learning resource file.
     */
    public function store(Request $request)
    {
        $userId = Auth::id();

        $lecturer = Lecturer::where('user_id', $userId)->firstOrFail();
        $staffNo = $lecturer->staffNo;

        $validated = $request->validate([
            'title'      => 'required|string|max:255',
            'courseCode' => 'required|string|max:50',
            'file'       => 'required|file|mimes:pdf,doc,docx,ppt,pptx,zip,png,jpg,jpeg|max:20480',
        ]);

        try {
            if ($request->hasFile('file') && $request->file('file')->isValid()) {
                $file = $request->file('file');
                
                // Store file in 'storage/app/public/resources'
                $path = $file->store('resources', 'public');

                $courseCode = trim(strtoupper($validated['courseCode']));

                // 1. Create Resource Entry
                $resource = Resource::create([
                    'staffNo'     => $staffNo,
                    'courseCode'  => $courseCode,
                    'title'       => $validated['title'],
                    'file_name'   => $file->getClientOriginalName(),
                    'file_path'   => $path,
                    'file_type'   => $file->getClientOriginalExtension(),
                    'uploaded_by' => $userId,
                ]);

                // 2. Create Announcement Entry
                Announcement::create([
                    'title'      => 'New Resource: ' . $validated['title'],
                    'courseCode' => $courseCode,
                    'file_path'  => $path,
                    'message'    => "A new learning resource has been published for your course.<br><a href='" . asset('storage/' . $path) . "' class='btn btn-sm btn-primary mt-2' download>Download " . htmlspecialchars($validated['title']) . "</a>",
                ]);

                if ($request->wantsJson()) {
                    return response()->json(['status' => 'success', 'resource' => $resource]);
                }

                return redirect()->back()->with('success', 'Learning resource uploaded and broadcasted as an announcement successfully!');
            }

            throw new Exception('The file failed to upload. PHP rejected the file upload payload.');

        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => $e->getMessage()], 422);
            }

            return redirect()->back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Remove the resource file from storage and delete the database entry.
     */
    public function destroy($id)
    {
        $resource = Resource::findOrFail($id);

        if ($resource->file_path && Storage::disk('public')->exists($resource->file_path)) {
            Storage::disk('public')->delete($resource->file_path);
        }

        $resource->delete();

        return redirect()->back()->with('success', 'Resource permanently deleted.');
    }
}