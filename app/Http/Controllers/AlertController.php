<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Product;
use App\Models\ProductInventory;

use Illuminate\Http\Request;

class AlertController extends Controller
{
    //
    public function getInsufficient()
    {

        $companyId = session('company_id');
        $insufficientMaterials = Material::with(['unit', 'supplier', 'category'])
            ->where('company_id', $companyId)
            ->whereNotNull('min_stock_level')
            ->whereColumn('current_stock_level', '<', 'min_stock_level')
            ->orderBy('category_id', 'asc')
            ->get();

        // Fetch insufficient products with related data
        $insufficientProducts = ProductInventory::where('min_stock_level', '>', 0)
        ->where('company_id', $companyId)
        ->whereColumn('current_stock_level', '<', 'min_stock_level')
        ->with([
            'product' => function($query) {
                $query->select('id', 'name', 'price', 'unit_id', 'category_id') // Select category_id to load the category relationship
                    ->with([
                        'unit:id,name', // Load the unit name
                        'category:id,name' // Load the category name
                    ]);
            },
            'location' => function($query) {
                $query->select('id', 'name'); // Assuming Location has 'id' and 'name' fields
            }
        ])
        ->get();
        return response()->json([
            'insufficientMaterials' => $insufficientMaterials,
            'insufficientProducts' => $insufficientProducts,
            'totalAlerts' => $insufficientMaterials->count() + $insufficientProducts->count(),
            'company_id' => $companyId
        ]);
    }
}
