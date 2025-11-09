<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TossedItem;
use App\Models\Material;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TossedItemController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'material_id' => 'required|exists:materials,id',
            'quantity'    => 'required|integer|min:1',
            'reason'      => 'nullable|string|max:500',
        ]);

        $companyId = session('company_id');
        $userId = Auth::id();

        if (!$companyId || !$userId) {
            return redirect()->back()->withErrors('Company ID or User ID is missing. Please log in again.');
        }

        // Ensure the material belongs to the user's company
        $material = Material::where('company_id', $companyId)->findOrFail($request->material_id);

        // Check if there's enough stock
        if ($material->current_stock_level < $request->quantity) {
            return redirect()->back()->withErrors('Insufficient stock. Current stock: ' . $material->current_stock_level);
        }

        // Deduct from material inventory
        $material->current_stock_level -= $request->quantity;
        $material->save();

        // Log the tossed item
        TossedItem::create([
            'company_id'  => $companyId,
            'user_id'     => $userId,
            'material_id' => $material->id,
            'quantity'    => $request->quantity,
            'reason'      => $request->reason,
        ]);

        return redirect()->back()->with('success', 'Tossed item logged successfully.');
    }

    public function index()
    {
        $companyId = session('company_id');
        
        if (!$companyId) {
            return redirect()->back()->withErrors('Company session not found. Please login again.');
        }

        // Get tossed items for this company only
        $tossedItems = TossedItem::forCompany($companyId)
            ->with(['material', 'user'])
            ->latest()
            ->get();

        // Calculate loss summary
        $lossSummary = $this->calculateLossSummary($companyId);

        return view('tossed_items.index', compact('tossedItems', 'lossSummary'));
    }

    public function destroy($id)
    {
        $companyId = session('company_id');
        
        if (!$companyId) {
            return redirect()->back()->withErrors('Company session not found. Please login again.');
        }

        // Ensure the tossed item belongs to the user's company
        $tossedItem = TossedItem::forCompany($companyId)->findOrFail($id);
        $material = $tossedItem->material;

        // Ensure the material belongs to the user's company
        if ($material->company_id !== $companyId) {
            return redirect()->back()->withErrors('Unauthorized action.');
        }

        // Restore the tossed amount
        $material->current_stock_level += $tossedItem->quantity;
        $material->save();

        $tossedItem->delete();

        return redirect()->back()->with('success', 'Tossed item deleted and stock restored.');
    }

    /**
     * Calculate loss summary for the company
     */
    private function calculateLossSummary($companyId)
    {
        $summary = TossedItem::select([
                'tossed_items.reason',
                DB::raw('COUNT(*) as total_incidents'),
                DB::raw('SUM(tossed_items.quantity) as total_quantity_lost'),
                DB::raw('SUM(tossed_items.quantity * materials.price_per_unit) as total_value_lost')
            ])
            ->join('materials', 'tossed_items.material_id', '=', 'materials.id')
            ->where('tossed_items.company_id', $companyId)
            ->groupBy('tossed_items.reason')
            ->orderByDesc('total_value_lost')
            ->get();

        $totalLoss = TossedItem::select([
                DB::raw('COUNT(*) as total_incidents'),
                DB::raw('SUM(tossed_items.quantity) as total_quantity_lost'),
                DB::raw('SUM(tossed_items.quantity * materials.price_per_unit) as total_value_lost')
            ])
            ->join('materials', 'tossed_items.material_id', '=', 'materials.id')
            ->where('tossed_items.company_id', $companyId)
            ->first();

        return [
            'by_reason' => $summary,
            'total' => $totalLoss
        ];
    }
}