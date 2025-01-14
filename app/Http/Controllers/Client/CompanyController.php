<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CompanySetting;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    // Display the company settings page
    public function index()
    {
        // Retrieve the company settings for the authenticated user
        $companySettings = CompanySetting::where('user_id', auth()->id())->first();

        // Pass the existing company settings (if any) to the view
        return view('client.pages.company.setting', compact('companySettings'));
    }

    // Update or create company settings
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'company_name' => 'required|string|max:255',
            'custom_domain' => 'nullable|string|max:255',
            'domain_verified'=>'',
            'timezone' => 'required|string',
            'sidebar_color' => 'nullable|string|max:7',
            'accent_color' => 'nullable|string|max:7',
            'contact_link' => 'nullable|url',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'favicon' => 'nullable|image|mimes:jpg,jpeg,png,ico|max:2048',
            'application_icon' => 'nullable|image|mimes:jpg,jpeg,png,ico|max:2048',
            'sidebar_logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'spp_linkback' => ''
        ]);

        //echo "<pre>"; print_r($validatedData); die;
        // Check if settings exist for the current user
        $companySettings = CompanySetting::where('user_id', auth()->id())->first();

        // Handle file uploads
        if ($request->hasFile('logo')) {
            if ($companySettings && $companySettings->logo) {
                Storage::disk('public')->delete($companySettings->logo); // Delete old file
            }
            $validatedData['logo'] = $request->file('logo')->store('logos', 'public');
        }

        if ($request->hasFile('favicon')) {
            if ($companySettings && $companySettings->favicon) {
                Storage::disk('public')->delete($companySettings->favicon); // Delete old file
            }
            $validatedData['favicon'] = $request->file('favicon')->store('favicons', 'public');
        }

        if ($request->hasFile('application_icon')) {
            if ($companySettings && $companySettings->application_icon) {
                Storage::disk('public')->delete($companySettings->application_icon); // Delete old file
            }
            $validatedData['application_icon'] = $request->file('application_icon')->store('application_icons', 'public');
        }

        if ($request->hasFile('sidebar_logo')) {
            if ($companySettings && $companySettings->sidebar_logo) {
                Storage::disk('public')->delete($companySettings->sidebar_logo); // Delete old file
            }
            $validatedData['sidebar_logo'] = $request->file('sidebar_logo')->store('sidebar_logos', 'public');
        }

        if(isset($validatedData['spp_linkback'])){
            $validatedData['spp_linkback'] = 1;
        }else{
            $validatedData['spp_linkback'] = 0;
        }

        // Update or create the settings
        if ($companySettings) {
            $companySettings->update($validatedData);
        } else {
            $validatedData['user_id'] = auth()->id();
            CompanySetting::create($validatedData);
        }

        return redirect()->route('company.list')->with('success', 'Company settings updated successfully.');
    }

    public function removeImage(Request $request)
    {
        $validatedData = $request->validate([
            'type' => 'required|in:logo,favicon,application_icon,sidebar_logo',
        ]);

        $companySettings = CompanySetting::where('user_id', auth()->id())->first();
        if (!$companySettings) {
            return redirect()->route('company.list')->with('error', 'No company settings found.');
        }

        $field = $validatedData['type'];
        //echo "<pre>"; print_r($validatedData); die;

        if ($companySettings->$field) {
            // Delete the file from storage
            Storage::disk('public')->delete($companySettings->$field);

            // Update the field to null
            $companySettings->update([$field => null]);
        }

        return redirect()->route('company.list')->with('success', ucfirst($field) . ' removed successfully.');
    }

}