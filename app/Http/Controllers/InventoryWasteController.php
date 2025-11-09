<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TossedItem;

class InventoryWasteController extends Controller
{
    public function index()
    {
        $wasteItems = TossedItem::where('company_id', session('company_id'))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('Admin.inventory_waste', compact('wasteItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'material' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'required|string|max:50',
            'note' => 'nullable|string|max:255',
        ]);

        TossedItem::create([
            'company_id' => session('company_id'),
            'user_id' => auth()->id(),
            'material' => $request->material,
            'quantity' => $request->quantity,
            'unit' => $request->unit,
            'note' => $request->note,
        ]);

        return redirect()->route('inventory-waste.index')->with('success', 'Waste item logged.');
    }
}
