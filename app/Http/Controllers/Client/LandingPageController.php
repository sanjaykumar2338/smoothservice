<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\LandingPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LandingPageController extends Controller
{
    // Display a listing of the landing pages
    public function index()
    {
        $landingPages = LandingPage::whereNull('deleted_at')->paginate(10);
        return view('client.pages.landingpage.index', compact('landingPages'));
    }

    public function design(Request $request, $slug){
        $landingPage = LandingPage::where('slug',$slug)->first();
        if(!$landingPage){
            abort(404);
        }

        return view('client.pages.landingpage.design');
    }

    // Show the form for creating a new landing page
    public function create()
    {
        return view('client.pages.landingpage.create');
    }

    // Store a newly created landing page in storage
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255',
            'description' => '',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $imagePath = '';
        if($request->file('image')){
            $imagePath = $request->file('image')->store('landing_pages', 'public');
        }

        // Generate the slug based on the title
        $slug = \Str::slug($request->title, '-');

        LandingPage::create([
            'title' => $request->title,
            'description' => $request->description,
            'slug' => $slug,
            'is_visible' => $request->has('is_visible'),
            'show_in_sidebar' => $request->has('show_in_sidebar'),
            'show_coupon_field' => $request->has('show_coupon_field'),
            'fields' => $request->fields,
            'image' => $imagePath,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('landingpage.design', $slug);
        return redirect()->route('landingpage.list')->with('success', 'Landing page created successfully.');
    }

    // Show the form for editing a specific landing page
    public function edit($id)
    {
        $landingPage = LandingPage::findOrFail($id);
        return view('client.pages.landingpage.edit', compact('landingPage'));
    }

    public function update(Request $request, $id)
    {
        $landingPage = LandingPage::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Generate the slug based on the title
        $slug = \Str::slug($request->title, '-');

        // Handle image removal if the checkbox is checked
        if ($request->has('remove_image') && $landingPage->image && \Storage::exists('public/' . $landingPage->image)) {
            \Storage::delete('public/' . $landingPage->image);
            $landingPage->image = null;
        }

        // Handle new image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('landing_pages', 'public');
            $landingPage->image = $imagePath;
        }

        // Update other fields
        $landingPage->update([
            'title' => $request->title,
            'description' => $request->description,
            'slug' => $slug,
            'is_visible' => $request->has('is_visible'),
            'show_in_sidebar' => $request->has('show_in_sidebar'),
            'show_coupon_field' => $request->has('show_coupon_field'),
            'fields' => $request->fields,
            'image' => $landingPage->image, // Ensure the updated or null image is saved
        ]);

        return redirect()->route('landingpage.list')->with('success', 'Landing page updated successfully.');
    }

    // Soft delete the specified landing page
    public function destroy($id)
    {
        $landingPage = LandingPage::findOrFail($id);

        // Check if the landing page has an associated image
        if ($landingPage->image && \Storage::exists('public/' . $landingPage->image)) {
            \Storage::delete('public/' . $landingPage->image);
        }

        // Soft delete the landing page
        $landingPage->update(['deleted_at' => now()]);

        return redirect()->route('landingpage.list')->with('success', 'Landing page deleted successfully.');
    }
}
