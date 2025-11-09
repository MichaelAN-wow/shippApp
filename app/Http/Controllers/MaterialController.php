<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;
use App\Models\Material;
use App\Models\MaterialTransaction;
use App\Models\Unit;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\ProductMaterial;

class MaterialController extends Controller
{
    // public function __construct(){
    // 	$this->middleware('auth');
    // }

    public function store(Request $request)
    {
        $data = new Material;

        $data->material_code = $request->material_code;
        $data->name = $request->name;
        $data->material_type = $request->material_type;
        $data->unit_id = $request->unit;
        $data->min_stock_level = $request->min_stock_level;
        $data->current_stock_level = $request->current_stock_level;
        $data->price_per_unit = $request->isMaterialBase 
            ? (is_numeric($request->total) && is_numeric($request->total_weight) && floatval($request->total_weight) > 0 
                ? floatval($request->total) / floatval($request->total_weight) 
                : 0)
            : ($request->cost_per_unit ?? 0);
        $data->total_weight = $request->isMaterialBase ? floatval($request->total_weight) : null;
        $data->last_order_date = $request->last_order_date;
        $data->notes = $request->notes;
        $data->company_id = session('company_id');
        $data->material_base = $request->isMaterialBase ? true : false;

        // Handle the photo upload
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('materials_photos', 'public');
            $data->photo_path = $photoPath;
        }

        $categoryId = $request->category_id;
        if ($categoryId !== null) {
            $category = Category::where('id', $categoryId)->first();
            if (!$category) {
                // If the category does not exist, insert a new category
                $category = new Category();
                $category->type = 1;
                $category->name = $categoryId;
                $category->company_id = session('company_id');
                $category->save();
            }

            $data->category_id = $category->id;
        }

        $supplierId = $request->supplier_id;
        if ($supplierId !== null) {
            $supplier = Supplier::where('id', $supplierId)->first();
            if (!$supplier) {
                // If the category does not exist, insert a new category
                $supplier = new Supplier();
                $supplier->name = $supplierId;
                $supplier->company_id = session('company_id');
                $supplier->save();
            }

            $data->supplier_id = $supplier->id;
        }
        $data->save();

        $transaction = new MaterialTransaction;
        $transaction->material_id = $data->id;
        $transaction->quantity = $request->current_stock_level;
        $transaction->to_level = $request->current_stock_level;
        $transaction->unit_price = $request->cost_per_unit;
        $transaction->transaction_type = 1; //import
        $transaction->company_id = session('company_id');
        $transaction->save();

         // Handle material base materials if this is a material base
         if ($data->material_base && $request->has('material_id')) {
            $materialIds = $request->material_id;
            $materialCounts = $request->material_count;
            $materialUnitIds = $request->material_unit_id;
            $unitPrices = $request->unit_price;

            foreach ($materialIds as $index => $materialId) {
                $productMaterial = new ProductMaterial;
                $productMaterial->product_id_material_base = $data->id;
                $productMaterial->product_id = $data->id;
                $productMaterial->material_id = $materialId;
                $productMaterial->used_amount = $materialCounts[$index];
                $productMaterial->unit_id = $materialUnitIds[$index];
                // Extract only the numeric value from unit_price (e.g., '2.71/Pieces' => 2.71)
                $unitPriceRaw = $unitPrices[$index];
                if (strpos($unitPriceRaw, '/') !== false) {
                    $unitPriceRaw = explode('/', $unitPriceRaw)[0];
                }
                $productMaterial->unit_price = str_replace(['$', ','], '', $unitPriceRaw);
                $productMaterial->company_id = session('company_id');
                $productMaterial->save();
            }
        }

        return Redirect()->route('all.materials');
    }

    public function allMaterials(Request $request)
    {
        $limit = $request->input('limit', 200); // Default to 10 if no limit is set
        $materials = Material::where('company_id', session('company_id'))->orderBy('name', 'asc')->paginate($limit);
        $units = Unit::all()->groupBy('type');
        $suppliers = Supplier::where('company_id', session('company_id'))->get();
        $categories = Category::where('company_id', session('company_id'))->where('type', 1)->get();
        $categoryId = 0;
        $all_materials = Material::where('company_id', session('company_id'))->with('unit')->orderBy('name', 'asc')->get();

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

        return view('Admin.all_material', compact('materials', 'units', "unitsJson", "suppliers", "categories", "limit", "categoryId", "all_materials"));
    }

    //redner materials with Category
    public function showMaterialsByCategory(Request $request, $id)
    {
        $limit = $request->input('limit', 200); // Default to 10 if no limit is set
        $materials = Material::where('company_id', session('company_id'))->where('category_id', $id)->paginate($limit);
        $all_materials = Material::where('company_id', session('company_id'))->with('unit')->orderBy('name', 'asc')->get();

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

        $suppliers = Supplier::where('company_id', session('company_id'))->get();
        $categories = Category::where('company_id', session('company_id'))->get();
        $categoryId = $id;
        return view('Admin.all_material', compact('materials', 'units', 'unitsJson', "suppliers", "categories", "limit", "categoryId", "all_materials"));
    }



    public function destroy($id)
    {

        // Find the material by ID
        $material = Material::findOrFail($id);

        // Perform the deletion
        $material->delete();

        // Return a JSON response
        return response()->json(['success' => true, 'message' => 'Material deleted successfully.']);
    }

    public function getMaterialById($id)
    {
        $material = Material::with(['unit', 'supplier', 'category'])->find($id);
        if ($material) {
            $material->material_materials = ProductMaterial::where(function ($query) use ($material) {
                $query->where('product_id', $material->id)
                      ->where('product_id_material_base', $material->id);
            })
            ->with(['material.unit'])
            ->get();
            return response()->json($material);
        } else {
            return response()->json(['error' => 'Material not found'], 404);
        }
    }

    public function updateMaterialById(Request $request, Material $material)
    {
        // Handle file upload if present
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($material->photo_path) {
                Storage::disk('public')->delete($material->photo_path);
            }
            // Store new photo
            $photoPath = $request->file('photo')->store('materials_photos', 'public');
            $material->photo_path = $photoPath;
        }
        // Validate and update the material
        $material->update([
            'material_code' => $request->material_code,
            'name' => $request->name,
            'material_type' => $request->material_type,
            'unit_id' => $request->unit,
            'min_stock_level' => $request->min_stock_level,
            'current_stock_level' => $request->current_stock_level,
            'price_per_unit' => $request->isMaterialBase 
                ? (is_numeric($request->total) && is_numeric($request->total_weight) && floatval($request->total_weight) > 0 
                    ? floatval($request->total) / floatval($request->total_weight) 
                    : 0)
                : $request->cost_per_unit,
            'total_weight' => $request->isMaterialBase ? floatval($request->total_weight) : null,
            'last_order_date' => $request->last_order_date,
            'notes' => $request->notes,
            'photo_path' => $material->photo_path ?? $material->photo_path,
            'material_base' => $request->isMaterialBase ? true : false
        ]);

        // Handle category
        $categoryId = $request->category_id;
        if ($categoryId !== null) {
            $category = Category::firstOrCreate(['id' => $categoryId], ['name' => $categoryId, 'type' => 1]);
            $material->category_id = $category->id;
        }

        // Handle supplier
        $supplierId = $request->supplier_id;
        if ($supplierId !== null) {
            $supplier = Supplier::firstOrCreate(['id' => $supplierId], ['name' => $supplierId]);
            $material->supplier_id = $supplier->id;
        }

        $material->save();

        // Handle material base materials if this is a material base
        if ($material->material_base && $request->has('material_id')) {
            // Delete existing material base components
            ProductMaterial::where('product_id', $material->id)
                ->where('product_id_material_base', $material->id)
                ->delete();

            $materialIds = $request->material_id;
            $materialCounts = $request->material_count;
            $materialUnitIds = $request->material_unit_id;
            $unitPrices = $request->unit_price;

            foreach ($materialIds as $index => $materialId) {
                $productMaterial = new ProductMaterial;
                $productMaterial->product_id_material_base = $material->id;
                $productMaterial->product_id = $material->id;
                $productMaterial->material_id = $materialId;
                $productMaterial->used_amount = $materialCounts[$index];
                $productMaterial->unit_id = $materialUnitIds[$index];
                // Extract only the numeric value from unit_price (e.g., '2.71/Pieces' => 2.71)
                $unitPriceRaw = $unitPrices[$index];
                if (strpos($unitPriceRaw, '/') !== false) {
                    $unitPriceRaw = explode('/', $unitPriceRaw)[0];
                }
                $productMaterial->unit_price = str_replace(['$', ','], '', $unitPriceRaw);
                $productMaterial->company_id = session('company_id');
                $productMaterial->save();
            }
        }

        return response()->json([
            'success' => true,
            'material' => $material,
        ]);
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

                $supplierName = $row[7];

                if ($supplierName !== '') {
                    $supplier = Supplier::firstOrCreate(['name' => $supplierName]);
                    $supplierId = $supplier->id;
                } else {
                    // Handle the case when $supplierName is empty
                    $supplierId = null; // Or any other default value you prefer
                }

                $categoryName = $row[8];

                if ($categoryName !== '') {
                    $category = Category::firstOrCreate(['name' => $categoryName, 'type' => 1]);
                    $categoryId = $category->id;
                } else {
                    // Handle the case when $categoryName is empty
                    $categoryId = null; // Or any other default value you prefer
                }

                $lastOrderDate = $row[9] !== '' ? Carbon::createFromFormat('Y-m-d', $row[9])->toDateTimeString() : null;

                $notes = $row[10];
                // Create a new Material instance and insert into database
                $data = new Material;
                $data->name = $name;
                $data->current_stock_level = $current_stock_level;
                $data->min_stock_level = $min_stock_level;
                $data->unit_id = $unitId;
                $data->price_per_unit = $pricePerUnit;
                $data->supplier_id = $supplierId;
                $data->category_id = $categoryId;
                $data->last_order_date = $lastOrderDate;
                $data->material_code = $sku;
                $data->notes = $notes;
                $data->company_id = session('company_id');

                $data->save();

                $transaction = new MaterialTransaction;
                $transaction->material_id = $data->id;
                $transaction->user_id = Auth::id();
                $transaction->quantity = $current_stock_level;
                $transaction->to_level = $current_stock_level;
                $transaction->unit_price = $pricePerUnit;
                $transaction->transaction_type = 1; //import
                $transaction->company_id = session('company_id');
                $transaction->save();
            }
            return Redirect()->route('all.materials');
        } else {
            return "No file uploaded!";
        }
    }

    public function getPageScroll(Request $request)
    {
        if ($request->ajax()) {
            $limit = 200; // Default to 10 if no limit is set
            $categoryId = (int) $request->query('categoryId');
            $search = $request->input('search'); // Search term

            // Base query
            $query = Material::where('company_id', session('company_id'));

            // Filter by category if categoryId is provided
            if ($categoryId != 0) {
                $query->where('category_id', $categoryId);
            }

            // Search filter
            if (!empty($search)) {
                $query->where('name', 'LIKE', "%{$search}%");
                $limit = 10000;
            }

            // Order by name
            $query->orderBy('name', 'asc');

            // Get the materials
            $materials = $query->paginate($limit);

            // Render the view for your frontend
            return view('Admin.materials_table', compact('materials'))->render();
        }
    }
}