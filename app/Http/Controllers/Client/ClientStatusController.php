<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClientStatus;

class ClientStatusController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $clientStatuses = ClientStatus::when($search, function($query, $search) {
            return $query->where('label', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%");
        })->paginate(8);

        return view('client.pages.settings.clientstatuses.index', compact('clientStatuses', 'search'));
    }


    public function create()
    {
        return view('client.pages.settings.clientstatuses.add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|unique:client_statuses|max:255',
            'color' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        ClientStatus::create($request->all());
        return redirect()->route('client.statuses.list')->with('success', 'Client Status created successfully');
    }

    public function edit($id)
    {
        $status = ClientStatus::findOrFail($id);
        return view('client.pages.settings.clientstatuses.edit', compact('status'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'label' => 'required|unique:client_statuses,label,' . $id . '|max:255',
            'color' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $status = ClientStatus::findOrFail($id);
        $status->update($request->all());
        return redirect()->route('client.statuses.list')->with('success', 'Client Status updated successfully');
    }

    public function destroy($id)
    {
        $status = ClientStatus::findOrFail($id);
        $status->delete();
        return redirect()->route('client.statuses.list')->with('success', 'Client Status deleted successfully');
    }
}
