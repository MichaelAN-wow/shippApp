<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductTransaction;
use App\Models\Unit;
use App\Models\Material;
use App\Models\ProductMaterial;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\ShopifyLocation;
use App\Models\ProductInventory;

use Carbon\Carbon;

class ProductController extends Controller
{
    // public function __construct(){
    // 	$this->middleware('auth');
    // }

    public function store(Request $request)
    {

        $data = new Product;
        //dd($request);
        $data->product_code = $request->product_code;
        $data->name = $request->name;
        $data->product_type = $request->product_type;
        if ($request->product_type == 2) {
            $data->price = str_replace('$', '', $request->total); //made by me
        } else {
            $data->price = $request->cost_per_unit; //source from supplier
        }

        $data->unit_id = $request->unit_id;
        $data->batch_size = $request->batch_size;
        $data->min_stock_level = $request->min_stock_level;
        $data->current_stock_level = $request->current_stock_level;

        $data->notes = $request->notes;

        $categoryId = $request->category_id;
        if ($categoryId !== null) {
            $category = Category::where('id', $categoryId)->first();
            if (!$category) {
                // If the category does not exist, insert a new category
                $category = new Category();
                $category->type = 2; // 2 means produts
                $category->name = $categoryId;
                $category->save();
            }

            $data->category_id = $category->id;
        }

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('products_photos', 'public');
            $data->photo_path = $photoPath;
        }

        $data->company_id = session('company_id');

        $isMaterialBaseEnabled = $request->has('isMaterialBase') && $request->input('isMaterialBase') === 'on';
        $data->isMaterialBase = $isMaterialBaseEnabled;

        $data->save();
        
        $transaction = new ProductTransaction;
        $transaction->product_id = $data->id;
        $transaction->user_id = Auth::id();
        $transaction->quantity = $request->current_stock_level;
        $transaction->to_level = $request->current_stock_level;
        if ($request->product_type == 2) {
            $transaction->unit_price = str_replace('$', '', $request->total); //made by me
        } else {
            $transaction->unit_price = $request->cost_per_unit; //source from supplier
        }
        $transaction->transaction_type = 1; //import
        $transaction->company_id = session('company_id');
        $transaction->save();


        if ($request->product_type == 2) {
            $productId = $data->id;
            $materialIds = $request->material_id;
            $materialCounts = $request->material_count;
            $materialUnitIds = $request->material_unit_id;
            $materialPrice = str_replace(',', '', $request->sub_total);
            $isMaterialBase = $request->_isMaterialBase;

            for ($i = 0; $i < count($materialIds); $i++) {
                if ($materialCounts[$i] > 0) {
                    $productMaterial = new ProductMaterial;
                    $productMaterial->product_id = $productId;
                    if ($isMaterialBase[$i] == 1)
                        $productMaterial->product_id_material_base = $materialIds[$i];
                    else 
                        $productMaterial->material_id = $materialIds[$i];
                    $productMaterial->used_amount = $materialCounts[$i];
                    $productMaterial->unit_id = $materialUnitIds[$i];
                    $productMaterial->unit_price = str_replace('$', '', $materialPrice[$i]); //made by me
                    $productMaterial->company_id = session('company_id');
                    $productMaterial->save();
                }
            }
        }

        $locationIds = $request->location_id;
        $minStockLevels = $request->location_min_level;
        $currentStockLevels = $request->location_current_level;

        if (!is_null($locationIds) && count($locationIds) > 0) {
            for ($i = 0; $i < count($locationIds); $i++) {
                $locationId = $locationIds[$i];
                $minStockLevel = $minStockLevels[$i];
                $currentStockLevel = $currentStockLevels[$i];
        
                // Use firstOrCreate to either update an existing record or create a new one
                $productInventory = ProductInventory::firstOrCreate(
                    ['product_id' => $data->id, 'location_id' => $locationId],
                    ['min_stock_level' => $minStockLevel]
                );
        
                $productInventory->current_stock_level = $currentStockLevel;
                $productInventory->company_id = session('company_id');
                $productInventory->save();
            }
        }

        $product = Product::with('category', 'unit')->find($data->id);

        return response()->json([
            'success' => true,
            'product' => $product,
        ]);

        return Redirect()->route('all.products');
    }

    //render materials with Category
    public function showByCategory(Request $request, $id)
    {
        $limit = $request->input('limit', 100); // Default to 10 if no limit is set

        $products = Product::where('company_id', session('company_id'))->with('category', 'unit')->where('category_id', $id)->orderBy('Name')->get();

        $groupedProducts = $products->groupBy(function ($item) {
            return $item->variants_name ?: $item->id;
        });

        $flattenedProducts = new Collection();

        foreach ($groupedProducts as $key => $group) {
            if ($group->first()->variants_name) {
                $flattenedProducts->push(['variant' => $group->first(), 'products' => $group]); // Add the variant row
            } else {
                foreach ($group as $product) {
                    $flattenedProducts->push(['variant' => null, 'products' => collect([$product])]); // Add each product
                }
            }
        }

        // Create LengthAwarePaginator
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $flattenedProducts->slice(($currentPage - 1) * $limit, $limit)->all();
        $paginatedItems = new LengthAwarePaginator($currentItems, $flattenedProducts->count(), $limit);
        $paginatedItems->setPath($request->url());

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

        $materials = Material::where('company_id', session('company_id'))->with('unit')->orderBy('name', 'asc')->get();
        $materialBasedProducts = Product::where('company_id', session('company_id'))
            ->where('isMaterialBase', true)
            ->with('unit')
            ->select('*', 'price as price_per_unit')
            ->orderBy('name', 'asc')
            ->get();
        $materials = $materialBasedProducts->merge($materials);
        $productMaterials = ProductMaterial::where('company_id', session('company_id'))->get();
        $suppliers = Supplier::where('company_id', session('company_id'))->get();
        $categories = Category::where('company_id', session('company_id'))->where('type', 2)->get();
        $locations  = ShopifyLocation::where('company_id', session('company_id'))->get();
        $categoryId = $id;

        $material_bases = Product::where('company_id', session('company_id'))->where('isMaterialBase', 1)->with('category', 'unit')->get();

        return view('Admin.all_product', compact('paginatedItems', 'units', 'unitsJson', 'materials', "suppliers", 'productMaterials', "categories", "limit", "categoryId", "locations", 'material_bases'));
    }


    public function getAllProducts(Request $request)
    {
        $limit = $request->input('limit', 100); // Default to 10 if no limit is set

        $products = Product::where('company_id', session('company_id'))->with('category', 'unit')->get();
        $groupedProducts = $products->groupBy(function ($item) {
            return $item->variants_name ?: $item->id;
        });

        $flattenedProducts = new Collection();

        foreach ($groupedProducts as $key => $group) {
            if ($group->first()->variants_name) {
                $flattenedProducts->push(['variant' => $group->first(), 'products' => $group]); // Add the variant row
            } else {
                foreach ($group as $product) {
                    $flattenedProducts->push(['variant' => null, 'products' => collect([$product])]); // Add each product
                }
            }
        }

        // Create LengthAwarePaginator
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $flattenedProducts->slice(($currentPage - 1) * $limit, $limit)->all();
        $paginatedItems = new LengthAwarePaginator($currentItems, $flattenedProducts->count(), $limit);
        $paginatedItems->setPath($request->url());

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
        $materials = Material::where('company_id', session('company_id'))->with('unit')->orderBy('name', 'asc')->get();
        $materialBasedProducts = Product::where('company_id', session('company_id'))
            ->where('isMaterialBase', true)
            ->with('unit')
            ->select('*', 'price as price_per_unit')
            ->orderBy('name', 'asc')
            ->get();
        $materials = $materialBasedProducts->merge($materials);

        $productMaterials = ProductMaterial::where('company_id', session('company_id'))->get();
        $suppliers = Supplier::where('company_id', session('company_id'))->get();
        $categories = Category::where('company_id', session('company_id'))->where('type', 2)->get();
        $locations  = ShopifyLocation::where('company_id', session('company_id'))->get();
        $categoryId = 0;

        $material_bases = Product::where('company_id', session('company_id'))->where('isMaterialBase', 1)->with('category', 'unit')->get();

        return view('Admin.all_product', compact('paginatedItems', 'units', 'unitsJson', 'materials', "suppliers", 'productMaterials', "categories", "limit", "categoryId", "locations", 'material_bases'));
    }
    public function destroy($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            return response()->json(['success' => true, 'message' => 'Product deleted successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Product not found.']);
        }
    }

    public function upload_csv(Request $request)
    {
        if ($request->hasFile('csv_file')) {
            $file = $request->file('csv_file');
            // Store or process the file here
            $path = $file->getRealPath();

            $csvData = array_map('str_getcsv', file($path));
            array_shift($csvData);

            foreach ($csvData as $row) {
                // Assuming the structure of each row matches the CSV file columns you provided
                $name = $row[0];
                $current_stock_level = $row[1];
                $min_stock_level = $row[2] !== '' ? $row[2] : null;
                $unitName = $row[3];

                $unit = Unit::where('short_name', $unitName)->first();
                $unitId = $unit ? $unit->id : null;

                $pricePerUnit = $row[4] !== '' ? $row[4] : 0;

                $sku = $row[6];

                //$supplierName = $row[7];

                // if ($supplierName !== '') {
                //     $supplier = Supplier::firstOrCreate(['name' => $supplierName]);
                //     $supplierId = $supplier->id;
                // } else {
                //     // Handle the case when $supplierName is empty
                //     $supplierId = null; // Or any other default value you prefer
                // }

                $categoryName = $row[8];

                if ($categoryName !== '') {

                    $category = Category::firstOrCreate(['name' => $categoryName, 'type' => 2]); //product category
                    $categoryId = $category->id;
                    //dd($categoryName);
                } else {
                    // Handle the case when $categoryName is empty
                    $categoryId = null; // Or any other default value you prefer
                }

                //$lastOrderDate = $row[9] !== '' ? Carbon::createFromFormat('Y-m-d', $row[9])->toDateTimeString() : null;

                $notes = $row[10];
                // Create a new Material instance and insert into database
                $data = new Product;
                $data->name = $name;
                $data->current_stock_level = $current_stock_level;
                $data->min_stock_level = $min_stock_level;
                $data->product_type = 2; //made by me
                $data->unit_id = $unitId;
                $data->price = $pricePerUnit;
                $data->category_id = $categoryId;
                $data->product_code = $sku;
                $data->notes = $notes;
                $data->company_id = session('company_id');

                $data->save();
                // Product::create([
                //     'name' => $name,
                //     'current_stock_level' => $current_stock_level,
                //     'min_stock_level' => $min_stock_level,
                //     'product_type' => 2, //made by me 
                //     'unit_id' => $unitId,
                //     'price' => $pricePerUnit,
                //     //'supplier_id' => $supplierId,
                //     'category_id' => $categoryId,
                //     //'last_order_date' => $lastOrderDate,
                //     'product_code' => $sku,
                //     'notes' => $notes,
                // ]);

                $transaction = new ProductTransaction;
                $transaction->product_id = $data->id;
                $transaction->user_id = Auth::id();
                $transaction->quantity = $current_stock_level;
                $transaction->to_level = $current_stock_level;
                $transaction->unit_price = $pricePerUnit;
                $transaction->transaction_type = 1; //import
                $transaction->company_id = session('company_id');
                $transaction->save();
            }
            return Redirect()->route('all.products');
        } else {
            return "No file uploaded!";
        }
    }

    public function getProductById($id)
    {
        $product = Product::with([
            'productMaterials.material', 
            'productInventory'
        ])->find($id);
        if ($product) {
            return response()->json($product);
        } else {
            return response()->json(['error' => 'Product not found'], 404);
        }
    }

    public function updateProductById(Request $request, Product $product)
    {
        //try {
            // Handle file upload if present
            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($product->photo_path) {
                    Storage::disk('public')->delete($product->photo_path);
                }
                // Store new photo
                $photoPath = $request->file('photo')->store('materials_photos', 'public');
                $product->photo_path = $photoPath;
            }
            // Validate and update the product details
            $product->update([
                'product_type' => $request->product_type,
                'name' => $request->name,
                'unit_id' => $request->unit_id,
                'price' => $request->total,
                'min_stock_level' => $request->min_stock_level,
                'current_stock_level' => $request->current_stock_level,
                'notes' => $request->notes,
                'product_code' => $request->product_code,
                'photo_path' => $product->photo_path ?? $product->photo_path,
                'isMaterialBase' => $isMaterialBaseEnabled = $request->has('isMaterialBase') && $request->input('isMaterialBase') === 'on',
            ]);
           

            // Handle category
            $categoryId = $request->category_id;
            if ($categoryId !== null) {
                $category = Category::firstOrCreate(['id' => $categoryId], ['name' => $categoryId, 'type' => 2, 'company_id' => session('company_id')]);
                $product->category_id = $category->id;
            }

            // Handle supplier
            $supplierId = $request->supplier_id;
            if ($supplierId !== null) {
                $supplier = Supplier::firstOrCreate(['id' => $supplierId], ['name' => $supplierId, 'company_id' => session('company_id')]);
                $product->supplier_id = $supplier->id;
            }

            $product->save();
             
            // Handle materials
            $materialIds = $request->input('material_id', []);
            $materialUnitPrices = $request->input('sub_total', []);
            $materialCounts = $request->input('material_count', []);
            $materialUnitIds = $request->input('material_unit_id', []);
            $isMaterialBase = $request->input('_isMaterialBase', []);

            DB::transaction(function () use ($product, $materialIds, $materialUnitPrices, $materialCounts, $materialUnitIds, $isMaterialBase) {
                // Delete existing ProductMaterial records for the product
                ProductMaterial::where('product_id', $product->id)->delete();

                // Insert new ProductMaterial records
                foreach ($materialIds as $index => $materialId) {
                    $unitPrice = isset($materialUnitPrices[$index]) ? preg_replace('/[^\d.]/', '', $materialUnitPrices[$index]) : 0;


                    $usedAmount = isset($materialCounts[$index]) ? $materialCounts[$index] : 0;
                    $unitId = $materialUnitIds[$index];

                    // // Create a new ProductMaterial record
                    // ProductMaterial::create([
                    //     'product_id' => $product->id,
                    //     'material_id' => $materialId,
                    //     'unit_id' => $unitId,
                    //     'unit_price' => $unitPrice,
                    //     'used_amount' => $usedAmount,
                    //     'company_id' => session('company_id')
                    // ]);

                    $productMaterial = new ProductMaterial;
                    $productMaterial->product_id = $product->id;
                    if (isset($isMaterialBase[$index]) && $isMaterialBase[$index] == 1)
                        $productMaterial->product_id_material_base = $materialId;
                    else 
                        $productMaterial->material_id = $materialId;
                    $productMaterial->used_amount = $usedAmount;
                    $productMaterial->unit_id = $unitId;
                    $productMaterial->unit_price = $unitPrice;
                    $productMaterial->company_id = session('company_id');
                    $productMaterial->save();
                }
            });
            

            $locationIds = $request->location_id;
            $minStockLevels = $request->location_min_level;
            $currentStockLevels = $request->location_current_level;

            ProductInventory::where('product_id', $product->id)
                ->delete();

            if (!is_null($locationIds) && count($locationIds) > 0) {
                for ($i = 0; $i < count($locationIds); $i++) {
                    $locationId = $locationIds[$i];
                    $minStockLevel = $minStockLevels[$i];
                    $currentStockLevel = $currentStockLevels[$i];

                    // Use firstOrCreate to either update an existing record or create a new one
                    $productInventory = ProductInventory::create([
                        'product_id' => $product->id,
                        'location_id' => $locationId,
                        'min_stock_level' => $minStockLevel,
                        'current_stock_level' => $currentStockLevel,
                        'company_id' => session('company_id')
                    ]);
                }
            }

            $product = Product::with('category', 'unit')->find($product->id);

            return response()->json([
                'success' => true,
                'product' => $product,
            ]);
        // } catch (\Exception $e) {
        //     // Handle exception and return JSON error response
        //     return response()->json([
        //         'success' => false,
        //         'message' => $e,
        //         'error' => $e->getMessage()
        //     ], 500);
        // }
    }

    public function getPageScroll(Request $request)
    {
        if ($request->ajax()) {
            $limit = 50; // Default to 10 if no limit is set
            $categoryId = (int) $request->query('categoryId');
            $search = $request->query('search'); // Retrieve the search query

            $query = Product::where('company_id', session('company_id'))->with('category', 'unit');

            if ($categoryId !== 0) {
                $query->where('category_id', $categoryId);
            }

            // Search filter
            if (!empty($search)) {
                $query->where('name', 'LIKE', "%{$search}%");
                $limit = 10000;
            }

            $products = $query->get();

            $groupedProducts = $products->groupBy(function ($item) {
                return $item->variants_name ?: $item->id;
            });

            $sortedGroupedProducts = $groupedProducts->map(function ($group) {
                return $group->sortBy(function ($item) {
                    if (strpos($item->name, 'Single') !== false) {
                        return 1;
                    } elseif (strpos($item->name, 'Double') !== false) {
                        return 2;
                    } elseif (strpos($item->name, 'Triple') !== false) {
                        return 3;
                    } else {
                        return 0;
                    }
                });
            })->sortBy(function ($group, $key) {
                return $group->first()->variants_name ?: $key;
            });

            $flattenedProducts = new Collection();

            foreach ($sortedGroupedProducts as $key => $group) {
                if ($group->first()->variants_name) {
                    $flattenedProducts->push(['variant' => $group->first(), 'products' => $group]); // Add the variant row
                } else {
                    foreach ($group as $product) {
                        $flattenedProducts->push(['variant' => null, 'products' => collect([$product])]); // Add each product
                    }
                }
            }

            // Create LengthAwarePaginator
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $currentItems = $flattenedProducts->slice(($currentPage - 1) * $limit, $limit)->all();
            $paginatedItems = new LengthAwarePaginator($currentItems, $flattenedProducts->count(), $limit);
            $paginatedItems->setPath($request->url());

            if ($request->ajax()) {
                return view('Admin.products_table', compact('paginatedItems', 'currentPage'))->render();
            }

            return view('Admin.products_table', compact('paginatedItems', 'currentPage'))->render();
        }
    }

    public function combineVariants(Request $request)
    {
        $validatedData = $request->validate([
            'variant_name' => 'required|string|max:255',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        $variantName = $validatedData['variant_name'];
        $productIds = $validatedData['product_ids'];

        // Combine the selected products as variants under the given variant name
        // Your logic to handle combining the products goes here

        Product::where('company_id', session('company_id'))->whereIn('id', $productIds)->update(['variants_name' => $variantName]);


        return redirect()->back()->with('success', 'Products combined as variants successfully!');
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        $product->load('unit', 'category');

        $productMaterials = ProductMaterial::where('product_id', $id)
            ->with('material.unit')
            ->get();

        //$lastOrder = $product->orders()->orderBy('created_at', 'desc')->first();

        return response()->json([
            'product' => $product,
            'productMaterials' => $productMaterials,
            //'lastOrderDate' => $lastOrder ? $lastOrder->created_at : null,
        ]);
    }
}
