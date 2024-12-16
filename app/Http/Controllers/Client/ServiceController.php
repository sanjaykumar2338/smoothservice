<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceParentService;
use App\Models\ServiceTeamMember;
use App\Models\TeamMember;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::query();

        if ($request->has('search')) {
            $query->where('service_name', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        $services = $query->orderBy('id','desc')->where('user_id',getUserID())->paginate(10);

        return view('client.pages.service.index', compact('services'));
    }

    public function create()
    {
        $team_members = TeamMember::where('added_by', auth()->id())->get();
        return view('client.pages.service.create')->with('team_members', $team_members);
    }

    public function edit(Service $service)
    {
        $team_members = TeamMember::where('added_by', auth()->id())->get();
        $selected_members = $service->teamMembers->pluck('id')->toArray(); // Get the team members IDs already added
        return view('client.pages.service.edit', compact('service', 'team_members', 'selected_members'));
    }

    public function update(Request $request, Service $service)
    {
        // Validate the request
        $validatedData = $request->validate([
            'service_name' => 'required|string|max:255',
            'editor_content' => '',
            'addon' => 'boolean',
            'parent_services' => 'array',
            'group_multiple' => 'boolean',
            'assign_team_member' => 'boolean',
            'team_member' => 'array',
            'set_deadline_check' => 'boolean',
            'set_a_deadline' => 'nullable|integer',
            'set_a_deadline_duration' => 'nullable|string|in:days,hours',
            'pricing_option_data' => '',
            'one_time_service_currency' => '',
            'one_time_service_currency_value' => '',
            'multiple_orders' => '',
            'recurring_service_currency' => '',
            'recurring_service_currency_value' => '',
            'recurring_service_currency_every' => '',
            'recurring_service_currency_value_two' => '',
            'recurring_service_currency_value_two_type' => '',
            'with_trial_or_setup_fee' => 'boolean',
            'when_recurring_payment_received' => '',
            'when_recurring_payment_received_two_order_currency' => '',
            'when_recurring_payment_received_two_order_currency_value' => '',
            'total_requests' => '',
            'active_requests' => '',
            'show_in_the_service_page' => '',
            'trial_currency' => '',
            'trial_price' => '',
            'trial_for' => '',
            'trial_period' => '',
            'service_type' => '',
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
            'pricing_option_data' => $request->pricing_option_data,
            'one_time_service_currency' => $request->one_time_service_currency,
            'one_time_service_currency_value' => $request->one_time_service_currency_value,
            'multiple_orders' => $request->multiple_orders,
            'recurring_service_currency' => $request->recurring_service_currency,
            'recurring_service_currency_value' => $request->recurring_service_currency_value,
            'recurring_service_currency_every' => $request->recurring_service_currency_every,
            'recurring_service_currency_value_two' => $request->recurring_service_currency_value_two,
            'recurring_service_currency_value_two_type' => $request->recurring_service_currency_value_two_type,
            'with_trial_or_setup_fee' => $validatedData['with_trial_or_setup_fee'] ?? false,
            'when_recurring_payment_received' => $request->when_recurring_payment_received,
            'when_recurring_payment_received_two_order_currency' => $request->when_recurring_payment_received_two_order_currency,
            'when_recurring_payment_received_two_order_currency_value' => $request->when_recurring_payment_received_two_order_currency_value,
            'user_id' => getUserID(),
            'total_requests' => $validatedData['total_requests'],
            'active_requests' => $validatedData['active_requests'],
            'show_in_the_service_page' => $validatedData['show_in_the_service_page'] ?? false,
            'trial_currency' => $validatedData['trial_currency'],
            'trial_price' => $validatedData['trial_price'],
            'trial_for' => $validatedData['trial_for'],
            'trial_period' => $validatedData['trial_period'],
            'service_type' => $validatedData['service_type'],
        ]);

        // Sync parent services
        /*
        if ($request->has('parent_services')) {
            $service->parentServices()->sync($request->parent_services);
        }
        */

        // Sync team members
        if ($request->has('team_member')) {
            $service->teamMembers()->sync($request->team_member);
        }

        return redirect()->route('service.list')->with('success', 'Service updated successfully.');
    }

    public function store(Request $request)
    {
        //echo "<pre>"; print_r($request->all()); die;
        // Validate the request
        $validatedData = $request->validate([
            'service_name' => 'required|string|max:255',
            'editor_content' => '',
            'addon' => 'boolean',
            'parent_services' => 'array',
            'group_multiple' => 'boolean',
            'assign_team_member' => 'boolean',
            'team_member' => 'array',
            'set_deadline_check' => 'boolean',
            'set_a_deadline' => 'nullable|integer',
            'set_a_deadline_duration' => 'nullable|string|in:days,hours',
            'pricing_option_data' => '',
            'one_time_service_currency' => '',
            'one_time_service_currency_value' => '',
            'multiple_orders' => '',
            'recurring_service_currency' => '',
            'recurring_service_currency_value' => '',
            'recurring_service_currency_every' => '',
            'recurring_service_currency_value_two' => '',
            'recurring_service_currency_value_two_type' => '',
            'with_trial_or_setup_fee' => 'boolean',
            'when_recurring_payment_received' => '',
            'when_recurring_payment_received_two_order_currency' => '',
            'when_recurring_payment_received_two_order_currency_value' => '',
            'price_options' => 'nullable|json', // Add validation for JSON input
            'combinations' => 'nullable|json',  // Add validation for JSON input
            'total_requests' => '',
            'active_requests' => '',
            'show_in_the_service_page' => '',
            'trial_currency' => '',
            'trial_price' => '',
            'trial_for' => '',
            'trial_period' => '',
            'service_type' => '',
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
            'pricing_option_data' => $request->pricing_option_data,
            'one_time_service_currency' => $request->one_time_service_currency,
            'one_time_service_currency_value' => $request->one_time_service_currency_value,
            'multiple_orders' => $request->multiple_orders,
            'recurring_service_currency' => $request->recurring_service_currency,
            'recurring_service_currency_value' => $request->recurring_service_currency_value,
            'recurring_service_currency_every' => $request->recurring_service_currency_every,
            'recurring_service_currency_value_two' => $request->recurring_service_currency_value_two,
            'recurring_service_currency_value_two_type' => $request->recurring_service_currency_value_two_type,
            'with_trial_or_setup_fee' => $validatedData['with_trial_or_setup_fee'] ?? false,
            'when_recurring_payment_received' => $request->when_recurring_payment_received,
            'when_recurring_payment_received_two_order_currency' => $request->when_recurring_payment_received_two_order_currency,
            'when_recurring_payment_received_two_order_currency_value' => $request->when_recurring_payment_received_two_order_currency_value,
            'price_options' => $request->price_options, // Save price_options
            'combinations' => $request->combinations,   // Save combinations
            'user_id' => getUserID(),
            'total_requests' => $validatedData['total_requests'],
            'active_requests' => $validatedData['active_requests'],
            'show_in_the_service_page' => $validatedData['show_in_the_service_page'] ?? false,
            'trial_currency' => $validatedData['trial_currency'],
            'trial_price' => $validatedData['trial_price'],
            'trial_for' => $validatedData['trial_for'],
            'trial_period' => $validatedData['trial_period'],
            'service_type' => $validatedData['service_type'],
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
        */

        // Attach team members
        if ($request->has('team_member')) {
            foreach ($request->team_member as $teamMemberId) {
                ServiceTeamMember::create([
                    'service_id' => $service->id,
                    'team_member_id' => $teamMemberId,
                ]);
            }
        }
        
        return redirect()->route('service.list')->with('success', 'Service created successfully.');
    }

    public function destroy(Service $service)
    {
        if(!checkPermission('add_edit_delete_services')){
            return redirect()->route('service.intakeform.list')->with('error', 'No permission');
        }

        if(!$service){
            return redirect()->route('service.intakeform.list')->with('error', 'Intake form not found!');
        }
        
        $service->delete();
        return redirect()->route('service.list')->with('success', 'Service deleted successfully.');
    }

    public function saveOptions(Request $request)
    {
        // Validate incoming request data if necessary
        $validatedData = $request->validate([
            'price_options' => 'required|array', // Ensure price_options is an array
            'combinations' => 'required|array'   // Ensure combinations is an array
        ]);

        // Find the Service model instance you want to update
        $service = Service::find($request->service_id); // Replace with the correct identifier

        // Update the price_options and combinations fields
        $service->price_options = json_encode($validatedData['price_options']); // Store as JSON
        $service->combinations = json_encode($validatedData['combinations']);   // Store as JSON
        $service->save();

        return response()->json(['success' => true, 'message' => 'Options saved successfully.']);
    }

    public function getOptions($serviceId)
    {
        $service = Service::find($serviceId);

        if ($service) {
            // Decode the JSON fields
            $priceOptions = json_decode($service->price_options, true);
            $combinations = json_decode($service->combinations, true);

            // Extract recurring price options if they exist
            $recurringPriceOptions = [];
            $selectedRecurringPrice = null;

            if ($priceOptions) {
                foreach ($priceOptions as $menu) {
                    $recurringPriceOptions[] = $menu['optionTitle'];
                }

                // If there's a selected recurring price, set it
                $selectedRecurringPrice = $service->selected_recurring_price ?? null;
            }

            return response()->json([
                'price_options' => $priceOptions,
                'combinations' => $combinations,
                'recurringPriceOptions' => $recurringPriceOptions,
                'selectedRecurringPrice' => $selectedRecurringPrice
            ]);
        }

        return response()->json(['price_options' => [], 'combinations' => [], 'recurringPriceOptions' => [], 'selectedRecurringPrice' => null], 404);
    }
}