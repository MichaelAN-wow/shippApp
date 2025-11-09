<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MakeSheetEntry;
use App\Models\Product;

class MakeSheetController extends Controller
{
    // Load the Make Sheet view with product list + latest saved entry for this company
    public function index()
    {
        $companyId = session('company_id');
        $products = Product::where('company_id', $companyId)->orderBy('name')->get();
        $latestEntry = MakeSheetEntry::where('company_id', $companyId)->latest()->first();
        
        return view("make_sheet.index", compact("products", "latestEntry"));
    }

    // Save the sheet data when user submits manually or auto-save
    public function store(Request $request)
    {
        $companyId = session('company_id');
        
        // Check if there's an existing entry for this company
        $entry = MakeSheetEntry::where('company_id', $companyId)->latest()->first();
        
        if ($entry) {
            // Update existing entry
            $entry->update([
                'data' => $request->input('data'),
            ]);
        } else {
            // Create new entry
            $entry = MakeSheetEntry::create([
                'company_id' => $companyId,
                'data' => $request->input('data'),
            ]);
        }
        
        return response()->json(['success' => true, 'id' => $entry->id]);
    }
    
    // Auto-save functionality for real-time saving
    public function autoSave(Request $request)
    {
        $companyId = session('company_id');
        
        // Find or create the entry for this company
        $entry = MakeSheetEntry::where('company_id', $companyId)->latest()->first();
        
        if ($entry) {
            // Update existing entry
            $entry->update([
                'data' => $request->input('data'),
            ]);
        } else {
            // Create new entry
            $entry = MakeSheetEntry::create([
                'company_id' => $companyId,
                'data' => $request->input('data'),
            ]);
        }
        
        return response()->json(['success' => true]);
    }

    // Optional: Future production ticket trigger
    public function submit(Request $request)
    {
        return response()->json(["message" => "Production ticket created!"]);
    }
}