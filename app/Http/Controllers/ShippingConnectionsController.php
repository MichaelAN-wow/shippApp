<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShippingConnection;

class ShippingConnectionsController extends Controller
{
    public function index()
    {
        $connections = ShippingConnection::orderBy('created_at', 'desc')->paginate(15);
        return view('shipping.connections', compact('connections'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'carrier'        => 'required|string|max:50',
            'account_number' => 'required|string|max:100',
            'api_key'        => 'nullable|string|max:255',
            'api_secret'     => 'nullable|string|max:255',
            'sandbox'        => 'required|boolean',
        ]);

        ShippingConnection::create($request->only([
            'carrier','account_number','api_key','api_secret','sandbox'
        ]));

        return back()->with('success', 'Connection added.');
    }

    public function update(Request $request, ShippingConnection $connection)
    {
        $request->validate([
            'carrier'        => 'required|string|max:50',
            'account_number' => 'required|string|max:100',
            'api_key'        => 'nullable|string|max:255',
            'api_secret'     => 'nullable|string|max:255',
            'sandbox'        => 'required|boolean',
        ]);

        $connection->update($request->only([
            'carrier','account_number','api_key','api_secret','sandbox'
        ]));

        return back()->with('success', 'Connection updated.');
    }

    public function destroy(ShippingConnection $connection)
    {
        $connection->delete();
        return back()->with('success', 'Connection deleted.');
    }
}
