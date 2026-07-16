<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GroupDiscussion;
use App\Models\User;

class GroupDiscussionController extends Controller
{
    /**
     * 🌐 View all groups in the system (The marketplace discovery page)
     */
    public function index()
    {
        // Fetch all groups along with member counts and load member details for management panels
        $groups = GroupDiscussion::with(['members'])->withCount('members')->get();
        
        return view('groups.index', compact('groups'));
    }

    /**
     * Show the group creation form view interface
     */
    public function create()
    {
        return view('groups.create');
    }

    /**
     * Handles the group creation form submission data
     */
    public function store(Request $request)
    {
        // 1. Validate the form inputs
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        // 2. Create the row in the group_discussions table
        $group = GroupDiscussion::create([
            'user_id' => auth()->id(), // Keeps track of who created/owns the group
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        // 🌟 3. Auto-join the creator to their own group membership table roster instantly
        $group->members()->attach(auth()->id());

        // 4. Redirect directly to the newly created group workspace channel
        return redirect()->route('chat.index', ['type' => 'group', 'id' => $group->id])
            ->with('success', 'Group created successfully!');
    }

    /**
     * 🤝 Standard Users: Handle hitting the Join or Leave toggle action button
     */
    public function join($id)
    {
        $group = GroupDiscussion::findOrFail($id);
        
        // Safety guard check: Prevent creators from leaving their own groups via standard action buttons
        if ($group->user_id === auth()->id()) {
            return back()->with('error', 'As the creator, you cannot leave this group. Delete it instead.');
        }

        // Toggles user entry in pivot table. Enforces unique instances on database levels safely.
        $group->members()->toggle(auth()->id());

        return back()->with('success', 'Group membership status updated!');
    }

    /**
     * 🔴 Creator Only: Permanently annihilate the entire group channel workspace
     */
    public function destroy($id)
    {
        $group = GroupDiscussion::findOrFail($id);

        if ($group->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action. Only the group creator can delete this.');
        }

        // Cascades down, automatically liquidating relationship pivot profiles in the background
        $group->delete(); 

        return redirect()->route('groups.index')->with('success', 'Group deleted successfully!');
    }

    /**
     * 👤 Creator Only: Forcefully kick/remove a specific individual member from the group roster
     */
    public function removeUser($groupId, $userId)
    {
        $group = GroupDiscussion::findOrFail($groupId);

        if ($group->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action. Only the group creator can manage members.');
        }

        // Detaches only the targeted student ID membership profile link
        $group->members()->detach($userId);

        return back()->with('success', 'User removed from group.');
    }
}