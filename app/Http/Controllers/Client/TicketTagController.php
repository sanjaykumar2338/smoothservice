<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TicketTag;

class TicketTagController extends Controller
{
    // List all tags
    public function index(Request $request)
    {
        $search = $request->input('search'); // Get the search input

        $tags = TicketTag::when($search, function ($query, $search) {
            return $query->where('name', 'LIKE', "%{$search}%");
        })->where('added_by', auth()->id())->paginate(8); // Apply search and paginate results

        return view('client.pages.settings.ticket_tags.index', compact('tags', 'search'));
    }

    // Show the form to create a new tag
    public function create()
    {
        return view('client.pages.settings.ticket_tags.add');
    }

    // Store a new tag in the database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:ticket_tags,name|max:255',
        ]);

        // Create new TicketTag
        TicketTag::create([
            'name' => $request->name,
            'added_by' => auth()->id()
        ]);

        return redirect()->route('tickettags.list')->with('success', 'Ticket Tag created successfully');
    }

    // Show the form to edit an existing tag
    public function edit($id)
    {
        $tag = TicketTag::findOrFail($id);
        return view('client.pages.settings.ticket_tags.edit', compact('tag'));
    }

    // Update an existing tag in the database
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:ticket_tags,name,' . $id . '|max:255',
        ]);

        $tag = TicketTag::findOrFail($id);
        $tag->update([
            'name' => $request->name,
        ]);

        return redirect()->route('tickettags.list')->with('success', 'Ticket Tag updated successfully');
    }

    // Delete a tag from the database
    public function destroy($id)
    {
        $tag = TicketTag::findOrFail($id);
        $tag->delete();

        return redirect()->route('tickettags.list')->with('success', 'Ticket Tag deleted successfully');
    }
}
