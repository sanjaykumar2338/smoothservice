<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\RoleAccess;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    // Show the form to create a new role
    public function create()
    {
        return view('client.pages.settings.roles.add');
    }

    // Store a new role and its permissions
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'role_name' => 'required|string|max:255', // Role name is required
            'permissions' => 'required|array',   // Permissions must be an array
        ]);

        // Create the role
        $role = Role::create(['name' => $request->role_name]);

        // Iterate over the permissions array and save each permission to role_access
        foreach ($request->permissions as $permission) {
            RoleAccess::create([
                'role_id' => $role->id,             // ID of the role we just created
                'access_name' => $permission,       // Name of the permission group (like 'order_access')
                'can_view' => isset($request->input('can_view')[$permission]) ? 1 : 0,
                'can_add' => isset($request->input('can_add')[$permission]) ? 1 : 0,
                'can_edit' => isset($request->input('can_edit')[$permission]) ? 1 : 0,
                'can_delete' => isset($request->input('can_delete')[$permission]) ? 1 : 0,
            ]);
        }

        // Redirect with success message
        return redirect()->route('client.roles.list')->with('success', 'Role created successfully.');
    }

    // Show the list of roles
    public function index()
    {
        $roles = Role::paginate(10); // Fetch all roles with pagination
        return view('client.pages.settings.roles.index', compact('roles'));
    }

    // Show the form to edit a role
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        
        // Get all access names associated with this role
        $roleAccess = RoleAccess::where('role_id', $id)->pluck('access_name')->toArray();

        return view('client.pages.settings.roles.edit', compact('role', 'roleAccess'));
    }

    // Update the role and its permissions
    public function update(Request $request, $id)
    {
        $request->validate([
            'role_name' => 'required|string|max:255',
            'permissions' => 'required|array',
        ]);

        // Update the role name
        $role = Role::findOrFail($id);
        $role->update(['name' => $request->role_name]);

        // Clear existing permissions for this role
        RoleAccess::where('role_id', $id)->delete();

        // Re-save permissions
        foreach ($request->permissions as $permission) {
            // Determine if the permission was checked or unchecked
            RoleAccess::create([
                'role_id' => $role->id,
                'access_name' => $permission,
                'can_view' => isset($request->input('can_view')[$permission]) ? 1 : 0,
                'can_add' => isset($request->input('can_add')[$permission]) ? 1 : 0,
                'can_edit' => isset($request->input('can_edit')[$permission]) ? 1 : 0,
                'can_delete' => isset($request->input('can_delete')[$permission]) ? 1 : 0,
            ]);
        }

        return redirect()->route('client.roles.list')->with('success', 'Role updated successfully.');
    }


    public function destroy($id)
    {
        // Find the role by its ID
        $role = Role::findOrFail($id);

        // Delete the role
        $role->delete();

        // Redirect back with a success message
        return redirect()->route('client.roles.list')->with('success', 'Role deleted successfully.');
    }
}
