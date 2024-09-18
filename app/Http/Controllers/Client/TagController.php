<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tag;

class TagController extends Controller
{
    // List all tags
    public function index(Request $request)
    {
        $search = $request->input('search'); // Get the search input

        $tags = Tag::when($search, function ($query, $search) {
            return $query->where('name', 'LIKE', "%{$search}%");
        })->paginate(8); // Apply search and paginate results

        return view('client.pages.settings.tags.index', compact('tags', 'search'));
    }


    // Show the form to create a new tag
    public function create()
    {
        return view('client.pages.settings.tags.add');
    }

    // Store a new tag in the database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:tags,name|max:255',
        ]);

        // Create new Tag
        Tag::create([
            'name' => $request->name,
        ]);

        return redirect()->route('client.tags.list')->with('success', 'Tag created successfully');
    }

    // Show the form to edit an existing tag
    public function edit($id)
    {
        $tag = Tag::findOrFail($id);
        return view('client.pages.settings.tags.edit', compact('tag'));
    }

    // Update an existing tag in the database
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:tags,name,' . $id . '|max:255',
        ]);

        $tag = Tag::findOrFail($id);
        $tag->update([
            'name' => $request->name,
        ]);

        return redirect()->route('client.tags.list')->with('success', 'Tag updated successfully');
    }

    // Delete a tag from the database
    public function destroy($id)
    {
        $tag = Tag::findOrFail($id);
        $tag->delete();

        return redirect()->route('client.tags.list')->with('success', 'Tag deleted successfully');
    }
}
