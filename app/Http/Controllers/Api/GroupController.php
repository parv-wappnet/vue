<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    // List all groups the user is part of
    public function index()
    {
        $groups = Group::whereJsonContains('members', Auth::id())->get();
        return response()->json($groups);
    }

    // Create a new group
    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'nullable|string',
            'members' => 'required|array|min:1',
            'members.*' => 'exists:users,id',
        ]);

        $group = Group::create([
            'admins' => [Auth::id()],
            'members' => array_unique(array_merge($validated['members'], [Auth::id()])),
            'description' => $validated['description'] ?? null,
            'created_by' => Auth::id(),
        ]);

        return response()->json(['message' => 'Group created', 'group' => $group], 201);
    }

    // Show a single group
    public function show($id)
    {
        $group = Group::findOrFail($id);

        if (!in_array(Auth::id(), $group->members)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($group);
    }

    // Add members to the group
    public function addMembers(Request $request, $id)
    {
        $group = Group::findOrFail($id);

        if (!in_array(Auth::id(), $group->admins)) {
            return response()->json(['message' => 'Only admins can add members'], 403);
        }

        $validated = $request->validate([
            'members' => 'required|array|min:1',
            'members.*' => 'exists:users,id',
        ]);

        $group->members = array_unique(array_merge($group->members, $validated['members']));
        $group->save();

        return response()->json(['message' => 'Members added', 'group' => $group]);
    }
}
