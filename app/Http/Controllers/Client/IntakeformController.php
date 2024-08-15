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
        $query = Intakeform::query();

        if ($request->has('search')) {
            $query->where('service_name', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        $intakeforms = $query->paginate(10);

        return view('client.pages.intakeform.index', compact('intakeforms'));
    }

    public function create()
    {
        return view('client.pages.intakeform.create');
    }

    public function edit(Intakeform $service, $id)
    {  
        $intakeform = Intakeform::find($id);
        return view('client.pages.intakeform.edit', compact('intakeform'));
    }

    public function update_intake(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'form_name' => 'required|string|max:255',
            'form_fields' => 'required', // JSON data from form builder
            'checkmark' => 'nullable|boolean',
            'onboarding_field' => 'nullable|array', // Validate as an array since multiple selections are possible
        ]);

        // Fetch the IntakeForm model you want to update
        $intakeForm = IntakeForm::findOrFail($request->id); // Assuming you have an ID to find the record

        // Update the form fields
        $intakeForm->form_name = $request->input('form_name');
        $intakeForm->form_fields = $request->input('form_fields'); // Store the form JSON data
        $intakeForm->checkmark = $request->input('checkmark') ? '1' : '0';
        $intakeForm->onboarding = $request->input('onboarding_field') ? implode(',', $request->input('onboarding_field')) : null; // Store as comma-separated values

        // Save the updated form
        $intakeForm->save();

        // Return a JSON response to the AJAX request
        return response()->json(['message' => 'Form updated successfully!']);
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'form_name' => 'required|string|max:255',
            'form_fields' => 'required', // Assuming this holds the JSON data
            'checkmark' => 'nullable|string',
            'onboarding_field' => 'nullable|array', // Validate as an array since multiple selections are possible
        ]);

        // Create a new IntakeForm instance and save the data
        $intakeForm = new IntakeForm();
        $intakeForm->user_id = auth()->id(); // Assuming the user is logged in
        $intakeForm->form_name = $request->input('form_name');
        $intakeForm->form_fields = $request->input('form_fields'); // Store the form JSON data
        $intakeForm->checkmark = $request->input('checkmark') ? '1' : '0';
        $intakeForm->onboarding = $request->input('onboarding_field') ? implode(',', $request->input('onboarding_field')) : null; // Store as comma-separated values or null
        $intakeForm->save();

        // Redirect back with a success message
        return redirect()->back()->with('status', 'Form has been saved successfully!');
    }

    public function destroy(Intakeform $intakeform, $id)
    {
        //echo "<pre>"; print_r($id); die;
        $rec = Intakeform::find($id);
        if(!$rec){
            return redirect()->route('client.service.intakeform.list')->with('error', 'Intake form not found!');
        }

        $rec->delete();
        return redirect()->route('client.service.intakeform.list')->with('success', 'Intake Form deleted successfully.');
    }
}