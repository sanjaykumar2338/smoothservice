<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Intakeform;
use App\Models\ServiceParentService;
use App\Models\ServiceTeamMember;

class IntakeformController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::query();

        if ($request->has('search')) {
            $query->where('service_name', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        $services = $query->paginate(10);

        return view('client.pages.intakeform.index', compact('services'));
    }

    public function create()
    {
        return view('client.pages.intakeform.create');
    }

    public function edit(Service $service)
    {
        return view('client.pages.service.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        // Validate the request
        $validatedData = $request->validate([
            'service_name' => 'required|string|max:255',
            'editor_content' => 'required|string',
            'addon' => 'boolean',
            'parent_services' => 'array',
            'group_multiple' => 'boolean',
            'assign_team_member' => 'boolean',
            'team_member' => 'array',
            'set_deadline_check' => 'boolean',
            'set_a_deadline' => 'nullable|integer',
            'set_a_deadline_duration' => 'nullable|string|in:days,hours',
        ]);

        // Update the service
        $service->update([
            'service_name' => $request->service_name,
            'description' => $request->editor_content,
            'addon' => $request->addon ?? false,
            'group_multiple' => $request->group_multiple ?? false,
            'assign_team_member' => $request->assign_team_member ?? false,
            'set_deadline_check' => $request->set_deadline_check ?? false,
            'set_a_deadline' => $request->set_a_deadline,
            'set_a_deadline_duration' => $request->set_a_deadline_duration,
        ]);

        // Sync parent services
        /*
        if ($request->has('parent_services')) {
            $service->parentServices()->sync($request->parent_services);
        }

        // Sync team members
        if ($request->has('team_member')) {
            $service->teamMembers()->sync($request->team_member);
        }
        */

        return redirect()->route('client.service.list')->with('success', 'Service updated successfully.');
    }

    public function store(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'service_name' => 'required|string|max:255',
            'editor_content' => 'required|string',
            'addon' => 'boolean',
            'parent_services' => 'array',
            'group_multiple' => 'boolean',
            'assign_team_member' => 'boolean',
            'team_member' => 'array',
            'set_deadline_check' => 'boolean',
            'set_a_deadline' => 'nullable|integer',
            'set_a_deadline_duration' => 'nullable|string|in:days,hours',
        ]);

        // Create the service
        $service = Service::create([
            'service_name' => $request->service_name,
            'description' => $request->editor_content,
            'addon' => $request->addon ?? false,
            'group_multiple' => $request->group_multiple ?? false,
            'assign_team_member' => $request->assign_team_member ?? false,
            'set_deadline_check' => $request->set_deadline_check ?? false,
            'set_a_deadline' => $request->set_a_deadline,
            'set_a_deadline_duration' => $request->set_a_deadline_duration,
            'user_id' => auth()->id(),
        ]);

        // Attach parent services
        /*
        if ($request->has('parent_services')) {
            foreach ($request->parent_services as $parentServiceId) {
                ServiceParentService::create([
                    'service_id' => $service->id,
                    'parent_service_id' => $parentServiceId,
                ]);
            }
        }

        // Attach team members
        if ($request->has('team_member')) {
            foreach ($request->team_member as $teamMemberId) {
                ServiceTeamMember::create([
                    'service_id' => $service->id,
                    'team_member_id' => $teamMemberId,
                ]);
            }
        }
        */
        
        return redirect()->route('client.service.list')->with('success', 'Service created successfully.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('client.service.list')->with('success', 'Service deleted successfully.');
    }
}