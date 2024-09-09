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

        if ($request->has('search')) {
            $query->where('first_name', 'like', '%' . $request->search . '%')
                ->orWhere('last_name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        $teamMembers = $query->with('role')->where('added_by', auth()->id())->orderBy('id', 'desc')->paginate(10);
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
        Mail::to($teamMember->email)->send(new TeamMemberRemoved($teamMember));

        $teamMember->delete();

        return redirect()->route('client.team.list')->with('success', 'Team member deleted successfully.');
    }
}