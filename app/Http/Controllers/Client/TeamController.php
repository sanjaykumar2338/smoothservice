<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TeamMember;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\TeamMemberWelcome;
use App\Mail\TeamMemberUpdated;
use App\Mail\TeamMemberRemoved;

class TeamController extends Controller
{
    // List all team members
    public function index(Request $request)
    {
        $query = TeamMember::query();

        // If the user is a 'team' member, filter by 'added_by' with the user who added the logged-in team member
        if (getUserType() == 'team') {
            // Get the user who added the logged-in team member
            $addedBy = TeamMember::where('id', getUserID())->value('added_by');
            // Only show team members added by the same user who added the logged-in team member
            $query->where('added_by', $addedBy);
        } else {
            // If the user is a web user, show team members they have added
            $query->where('added_by', auth()->id());
        }

        // Apply search filter if provided
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Fetch and paginate team members
        $teamMembers = $query->with('role')->orderBy('id', 'desc')->paginate(10);

        // Return the view with the filtered results
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
    
        // Generate a random password
        $password = \Str::random(8);
    
        // Hash the password
        $hashedPassword = Hash::make($password);
        //echo auth()->id(); die;
        // Create the new team member with hashed password
        $teamMember = TeamMember::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'role_id' => $validatedData['role_id'],
            'password' => $hashedPassword,
            'added_by' => auth()->id(),
        ]);
    
        // Fetch role name for the email content
        $roleName = Role::find($validatedData['role_id'])->name;
    
        // Send the welcome email with login details
        Mail::to($teamMember->email)->send(new TeamMemberWelcome($teamMember, $roleName, $password));
    
        return redirect()->route('client.team.list')->with('success', 'Team member added successfully, and email sent with login details.');
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

        // Check if anything has changed
        $changes = array_diff_assoc($validatedData, $teamMember->only(['first_name', 'last_name', 'email', 'role_id']));

        // Update team member details
        $teamMember->update($validatedData);

        // Send email notification only if there are changes
        if (!empty($changes)) {
            Mail::to($teamMember->email)->send(new TeamMemberUpdated($teamMember));
        }

        return redirect()->route('client.team.list')->with('success', 'Team member updated successfully.');
    }


    // Delete team member
    public function destroy(TeamMember $teamMember)
    {
        if(!checkPermission('add_edit_delete_team')){
            return redirect()->route('client.service.intakeform.list')->with('error', 'No permission');
        }

        Mail::to($teamMember->email)->send(new TeamMemberRemoved($teamMember));

        $teamMember->delete();

        return redirect()->route('client.team.list')->with('success', 'Team member deleted successfully.');
    }
}