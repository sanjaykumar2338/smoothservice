<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TeamMember;
use App\Models\Role;

class TeamController extends Controller
{
    // List all team members
    public function index(Request $request)
    {
        $query = TeamMember::query();

        if ($request->has('search')) {
            $query->where('first_name', 'like', '%' . $request->search . '%')
                ->orWhere('last_name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        $teamMembers = $query->with('role')->orderBy('id', 'desc')->paginate(10);

        return view('client.pages.team.index', compact('teamMembers'));
    }

    // Show form to create a new team member
    public function create()
    {
        $roles = Role::all();  // Fetch roles like Admin, Manager, Contractor
        return view('client.pages.team.add', compact('roles'));
    }

    // Store new team member
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:team_members',
            'role_id' => 'required|exists:roles,id',
        ]);

        TeamMember::create($validatedData);

        return redirect()->route('client.team.list')->with('success', 'Team member added successfully.');
    }

    // Show form to edit team member
    public function edit(TeamMember $teamMember)
    {
        //print_r($teamMember); die;
        $roles = Role::all();  // Fetch all available roles
        return view('client.pages.team.edit', compact('teamMember', 'roles'));
    }

    // Update team member
    public function update(Request $request, $id)
    {
        $teamMember = TeamMember::find($id);  // Find the team member by ID

        if (!$teamMember) {
            return redirect()->back()->with('error', 'Team member not found.');
        }

        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:team_members,email,' . $teamMember->id,
            'role_id' => 'required|exists:roles,id',
        ]);

        $teamMember->update($validatedData);

        return redirect()->route('client.team.list')->with('success', 'Team member updated successfully.');
    }

    // Delete team member
    public function destroy(TeamMember $teamMember)
    {
        $teamMember->delete();

        return redirect()->route('client.team.list')->with('success', 'Team member deleted successfully.');
    }
}