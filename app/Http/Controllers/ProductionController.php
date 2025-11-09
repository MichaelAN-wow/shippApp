<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use App\Models\Product;
use App\Models\Production;
use App\Models\ProductionProduct;
use App\Models\ProductTransaction;
use App\Models\MaterialTransaction;
use App\Models\Material;
use App\Models\ShopifyLocation;
use App\Models\Unit;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


use App\Http\Controllers\ShopifyController;

class ProductionController extends Controller
{

    protected $shopifyController;

    public function __construct(ShopifyController $shopifyController)
    {
        $this->shopifyController = $shopifyController;
    }

    public function getAllProduction()
    {        
        $products = Product::where('company_id', session('company_id'))->with('unit')->orderBy('name')->get();
        $productions = Production::where('company_id', session('company_id'))
        ->with([
            'production_product.product.productMaterials.material',
            'production_product.product.unit',
            'user' => function ($query) {
                // Include soft-deleted users as well
                $query->withTrashed();
            }
        ])
        ->orderBy('due_date', 'DESC')
        ->get();

        $fragrances = Material::whereHas('category', function ($query) {
            $query->where('company_id', session('company_id'))
                  ->where('type', 'material')
                  ->where('name', 'like', '%Fragrance%');
        })->when(!app()->runningInConsole(), function ($query) {
            $query->where('company_id', session('company_id'));
        })->get();

        $doughBowlOrCustomCategoryIds = Category::where('company_id', session('company_id'))
            ->where('type', 'product')
            ->where(function ($query) {
                $query->where('name', 'like', '%Dough Bowl Refill%')
                    ->orWhere('name', 'like', '%Custom Production%');
            })
            ->pluck('id');
        
        $doughBowlOrCustomCategoryIdsArray = $doughBowlOrCustomCategoryIds->toArray();
        
        $shopifyLocations = ShopifyLocation::where('company_id', session('company_id'))->orderBy('name')->get();

        $users = User::withTrashed()
            ->where('company_id', session('company_id'))
            ->whereIn('type', ['employee', 'super_admin'])
            ->orderBy('name')
            ->get(['id', 'name']);

        // Get weight units for fragrance
        $weightUnits = Unit::get()
            ->map(function ($unit) {
                return [
                    'id' => $unit->id,
                    'name' => $unit->name,
                    'short_name' => $unit->short_name,
                    'type' => $unit->type,
                    'conversion_factor' => $unit->conversion_factor
                ];
            });

        $units = Unit::all()->groupBy('type');
        $unitsJson = $units->map(function ($group) {
            return $group->map(function ($unit) {
                return [
                    'id' => $unit->id,
                    'name' => $unit->name,
                    'short_name' => $unit->short_name,
                    'type' => $unit->type,
                    'conversion_factor' => $unit->conversion_factor
                ];
            });
        });

        // Find the OZ unit to set as default
        $defaultUnitId = $weightUnits->where('short_name', 'oz')->first()['id'] ?? null;

        return view('Admin.all_production', compact(
            'products', 
            'productions', 
            'shopifyLocations', 
            'users', 
            'weightUnits',
            'defaultUnitId',
            'fragrances', 
            'unitsJson',
            'doughBowlOrCustomCategoryIds'
        ));
    }

    public function updateProduction(Request $request, $id)
    {
        $production = Production::find($id);

        if (!$production) {
            return response()->json(['message' => 'Production not found'], 404);
        }

        $production->update($request->only(['name', 'shopify_location_id', 'user_id', 'due_date', 'notes']));

        // Delete all existing ProductionProduct entries
        ProductionProduct::where('production_id', $id)->delete();

        // Update production products
        foreach ($request->product_id as $index => $productId) {
            $quantity = $request->product_quantity[$index];
            
            $productionProduct = new ProductionProduct([
                'production_id' => $id,
                'product_id' => $productId,
                'quantity' => $quantity,
                'company_id' => session('company_id')
            ]);

            // Check if this is a refill/custom spray product
            if (is_array($request->is_refill) && in_array($productId, $request->is_refill)) {
                // Find the matching index in the is_refill array
                $refillIndex = array_search($productId, $request->is_refill);
                if ($refillIndex !== false) {
                    $productionProduct->material_id = $request->fragrance_id[$refillIndex];
                    $productionProduct->material_quantity = $request->fragrance_amount_number[$refillIndex];
                    $productionProduct->material_unit_id = $request->fragrance_unit_id[$refillIndex];
                }
            }

            $productionProduct->save();
        }

        return Redirect()->route('all.production');
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $production = new Production();
        $production->name = $request->name;
        $production->user_id = $request->user_id;
        $production->notes = $request->notes;
        $production->due_date = $request->due_date;
        $production->shopify_location_id = $request->shopify_location_id;
        $production->company_id = session('company_id');
        $production->save();

        $productionId = $production->id;
        $productIds = $request->product_id;
        $productCounts = $request->product_quantity;
        $fragranceIds = $request->fragrance_id;  
        $fragrance_amount_number = $request->fragrance_amount_number;
        $fragrance_unit_ids = $request->fragrance_unit_id;
        $size = 0;
        $isRefill = $request->is_refill;

        if ($productIds !== null) {
            $size = count($productIds);
        }
        //dd($isRefill);
        for ($i = 0; $i < $size; $i++) {
            $items = new ProductionProduct;
            $items->production_id = $productionId;
            $items->product_id = $productIds[$i];
            $items->quantity = $productCounts[$i];
            $items->company_id = session('company_id');

            if (is_array($isRefill) || is_object($isRefill)) {
                foreach ($isRefill as $index => $productId) {
                    if ($productId == $productIds[$i]) {
                        $items->material_id = $fragranceIds[$index];
                        $items->material_quantity = $fragrance_amount_number[$index];
                        $items->material_unit_id = $fragrance_unit_ids[$index];
                    }
                }
            }

            $items->save();
        }
        return Redirect()->route('all.production');
    }

    public function update(Request $request)
    {

        $id = $request->id;
        $zone = $request->zone;
        if ($zone == 3) { //completed zone

        }
        $item = Production::find($id);
        if ($item) {
            // Update the item's status based on the zone
            $item->status = $zone; // Assuming the zone name corresponds to the status field
            $item->save();

            // Return a success response
            return response()->json(['message' => 'Item updated successfully'], 200);
        } else {
            // Return an error response if the item is not found
            return response()->json(['error' => 'Item not found'], 404);
        }
    }

    public function completeRun(Request $request)
    {
        $productionId = $request->input('production_id');
        $products = $request->input('products');
        $materials = $request->input('materials');

        $production = Production::find($productionId);
        if (!$production) {
            return response()->json(['success' => false, 'msg' => 'Production not found.']);
        }

        if ($production->is_completed == 0) {
            $production->is_completed = 1;
            $production->status = 3;

            DB::transaction(function () use ($productionId, $products, $materials, $production) {
                $failedProducts = [];
                $successfulProducts = [];

                $locationCache = [];
                // Process each product
                foreach ($products as $productData) {
                    $product = Product::find($productData['product_id']);
                    if ($product) {
                        if ($product->shopify_id) {
                            // If product is linked with Shopify, try to update Shopify stock
                            $result = $this->shopifyController->updateShopifyStockLevel(
                                $product, 
                                $productData['quantity'], 
                                $production->location->name,
                                $locationCache
                            );
                            // Add a delay to avoid hitting Shopify's rate limit (2 calls/sec)
                            usleep(600000); // 0.6 seconds
                            
                            if ($result) {
                                $successfulProducts[] = $productData;
                            } else {
                                $failedProducts[] = $product->name;
                            }
                        } else {
                            // If product is not linked with Shopify, just update local stock
                            $successfulProducts[] = $productData;
                        }
                    }
                }

                // If any Shopify updates failed, return error
                if (!empty($failedProducts)) {
                    return response()->json([
                        'success' => false,
                        'msg' => "Update stock level failed for the following products in Shopify: " . implode(', ', $failedProducts),
                    ]);
                }

                // Update local stock levels for all successful products
                foreach ($successfulProducts as $productData) {
                    $product = Product::find($productData['product_id']);
                    
                    // Update local stock level
                    $product->current_stock_level = $productData['to_level'];
                    $product->save();

                    // Record product transaction
                    ProductTransaction::create([
                        'production_id' => $productionId,
                        'product_id' => $productData['product_id'],
                        'before_level' => $productData['before_level'],
                        'to_level' => $productData['to_level'],
                        'quantity' => $productData['quantity'],
                        'unit_price' => $productData['unit_price'],
                        'transaction_type' => 2 // Assuming 2 means a production transaction
                    ]);
                }

                // Process materials
                if ($materials) {
                    foreach ($materials as $materialData) {
                        if (strpos($materialData['material_id'], '###') === 0) {
                            $productId = substr($materialData['material_id'], 3);
                            $product = Product::find($productId);
                            if ($product) {
                                $product->current_stock_level = $materialData['to_level'];
                                $product->save();
                            }
                        } else {
                            $material = Material::find($materialData['material_id']);
                            $material->current_stock_level = $materialData['to_level'];
                            $material->save();

                            MaterialTransaction::create([
                                'production_id' => $productionId,
                                'material_id' => $materialData['material_id'],
                                'before_level' => $materialData['before_level'],
                                'to_level' => $materialData['to_level'],
                                'quantity' => $materialData['quantity'],
                                'unit_price' => $materialData['unit_price'],
                                'transaction_type' => 2
                            ]);
                        }
                    }
                }

                // Save production status
                $production->save();
            });
        } else {
            $production->due_date = Carbon::now();
            $production->save();
        }

        return response()->json(['success' => true, 'msg' => 'Production completed successfully.']);
    }

    public function getProductionById($id)
    {
        $production = Production::with([
            'production_product.product.productMaterials.material',
            'production_product.product.unit',
            'user'
        ])->find($id);

        if (!$production) {
            return response()->json(['message' => 'Production not found'], 404);
        }

        // Calculate completed time and assigned to (assuming you have these fields)
        $completedTime = $production->due_date ? now()->diffInHours($production->due_date) : 'N/A';
        $assignedTo = $production->assigned_to->name ?? 'N/A';
        $location = $production->location != null ? $production->location->name : "No location available";
        
        return response()->json([
            'name' => $production->name,
            'location' => $location,
            'shopify_location_id' => $production->location->id ?? null,
            'user_id' => $production->user->id,
            'user_name' => $production->user->name,
            'due_date' => $production->due_date,
            'production_product' => $production->production_product->map(function ($productionProduct) {
                // Start the response for each production product
                $productResponse = [
                    'id' => $productionProduct->id,
                    'quantity' => $productionProduct->quantity,
                    'material_id' => $productionProduct->material_id,
                    'material_quantity' => $productionProduct->material_quantity,
                    'material_unit_id' => $productionProduct->material_unit_id,
                    'product' => [
                        'id' => $productionProduct->product->id,
                        'name' => $productionProduct->product->name,
                        'materials' => []
                    ]
                ];
        
                // Check if product has material_id (i.e., it's a material-based product)
                if ($productionProduct->material_id) {
                    // If material_id exists, fetch material data directly
                    $material = Material::find($productionProduct->material_id);
                    
                    if ($material) {
                        $productResponse['product']['name'] .= ' + ' . $material->name;
                        $productResponse['product']['materials'][] = [
                            'name' => $material->name,
                            'quantity' => $productionProduct->material_quantity,
                            'unit_id' => $material->unit_id
                        ];
                    } else {
                        // In case material is not found, return a default message
                        $productResponse['product']['materials'][] = [
                            'name' => 'Material not found',
                            'quantity' => $productionProduct->material_quantity ?? 0,
                            'unit_id' => null
                        ];
                    }
                }
        
                // If product doesn't have material_id, we still need to check productMaterials for any related materials
                $productMaterials = $productionProduct->product->productMaterials->map(function ($productMaterial) {
                    if ($productMaterial->product_id_material_base) {
                        // If there is a base product for the material, fetch it
                        $baseProduct = Product::find($productMaterial->product_id_material_base);
                        if ($baseProduct) {
                            return [
                                'name' => $baseProduct->name, // Get the name from the base product
                                'quantity' => $productMaterial->used_amount,
                                'unit_id' => $baseProduct->unit_id
                            ];
                        } else {
                            // If base product is not found, return a default response
                            return [
                                'name' => 'Base product not found',
                                'quantity' => $productMaterial->used_amount,
                                'unit_id' => null
                            ];
                        }
                    } else if (isset($productMaterial->material->name)) {
                        // If material_id is not set, use material data from the productMaterial relationship
                        return [
                            'name' => $productMaterial->material->name,
                            'quantity' => $productMaterial->used_amount,
                            'unit_id' => $productMaterial->unit_id
                        ];
                    }
                    return null; // return null if no material exists
                })->filter(); // Filter out null values (materials that couldn't be fetched)
        
                // Merge the found materials into the product response
                $productResponse['product']['materials'] = array_merge($productResponse['product']['materials'], $productMaterials->toArray());
                
                return $productResponse;
            }),
            'completed_time' => $completedTime,
            'assigned_to' => $assignedTo
        ]);
    }

    public function destroy($id)
    {
        try {
            $production = Production::findOrFail($id);
            $production->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function create_production_from_alert(Request $request)
    {

        // Create a new production entry
        $production = new Production();
        $production->name = 'Production from Alert'; // You can customize this as needed
        $production->user_id = Auth::id();
        $production->notes = 'Automatically generated from stock alerts.';
        $production->due_date = now()->addDays(7);
        $production->shopify_location_id = $request->shopify_location_id ?? null;
        $production->company_id = session('company_id');
        $production->save();

        // Get the production ID
        $productionId = $production->id;

        // Process the products array from the request
        $products = $request->products;
        
        if (!empty($products)) {
            
            foreach ($products as $productData) {
                
                // Create a new production product for each product in the array
                $productionProduct = new ProductionProduct();
                $productionProduct->production_id = $productionId;
                $productionProduct->product_id = $productData['product_id']; // Get product ID from the request data
                $productionProduct->quantity = $productData['min_stock_level'] - $productData['current_stock_level'];
                $productionProduct->company_id = session('company_id');
                
                $productionProduct->save();
            }
        }

        // Redirect or return a response after creating the production
        return response()->json(['success' => true, 'message' => 'Production created successfully']);
    }
}