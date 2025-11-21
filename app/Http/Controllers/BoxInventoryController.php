<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Box;

class BoxInventoryController extends Controller
{
    public function index()
    {
        $boxes = Box::orderByDesc('id')->get();
        return view('shipping.box_inventory', compact('boxes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'length'        => 'nullable|numeric',
            'height'        => 'nullable|numeric',
            'width'         => 'nullable|numeric',
            'empty_weight'  => 'nullable|numeric',
            'quantity'      => 'required|integer',
            'supplier'      => 'nullable|string|max:255',
            'cost'          => 'nullable|numeric',
        ]);

        Box::create($request->all());

        return redirect()->route('shipping.box_inventory.index')->with('success', 'Box added successfully!');
    }

    public function update(Request $request, $id)
    {
        $box = Box::findOrFail($id);

        $request->validate([
            'name'          => 'required|string|max:255',
            'length'        => 'nullable|numeric',
            'height'        => 'nullable|numeric',
            'width'         => 'nullable|numeric',
            'empty_weight'  => 'nullable|numeric',
            'quantity'      => 'required|integer',
            'supplier'      => 'nullable|string|max:255',
            'cost'          => 'nullable|numeric',
        ]);

        $box->update($request->all());

        return redirect()->route('shipping.box_inventory.index')->with('success', 'Box updated successfully!');
    }

    public function destroy($id)
    {
        $box = Box::findOrFail($id);
        $box->delete();

        return redirect()->route('shipping.box_inventory.index')->with('success', 'Box deleted successfully!');
    }
}
