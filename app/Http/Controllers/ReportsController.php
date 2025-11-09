<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use DatePeriod; // For native PHP DatePeriod
use DateInterval;

use Illuminate\Support\Facades\DB;

use App\Models\Product;
use App\Models\ProductTransaction;
use App\Models\Material;
use App\Models\Category;
use App\Models\MaterialTransaction;
use App\Models\Production;
use App\Models\ProductionProduct;
use App\Models\Sale;
use App\Models\SaleProduct;
use Illuminate\Http\Request;


class ReportsController extends Controller
{

    public function index()
    {

        $products = Product::where('company_id', session('company_id'))->with('unit')->get();
        $productions = Production::where('company_id', session('company_id'))->with('production_product')->get();
        return view('Admin.all_report', compact('products', "productions"));
    }

    public function store(Request $request)
    {
        $production = new Production();
        $production->name = $request->name;
        $production->notes = $request->notes;
        $production->due_date = $request->due_date;
        $production->company_id = session('company_id');
        $production->save();

        $productionId = $production->id;
        $productIds = $request->product_id;
        $productCounts = $request->product_quantity;
        $size = 0;

        if ($productIds !== null) {
            $size = count($productIds);
        }
        for ($i = 0; $i < $size; $i++) {
            $items = new ProductionProduct;
            $items->production_id = $productionId;
            $items->product_id = $productIds[$i];
            $items->quantity = $productCounts[$i];
            $items->company_id = session('company_id');
            $items->save();
        }
        return Redirect()->route('all.production');
    }

    public function update(Request $request)
    {

        $id = $request->id;
        $zone = $request->zone;

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

    public function getSalesData(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Retrieve sales data between start_date and end_date
        $startDate = Carbon::createFromFormat('Y-m-d', $request->input('start_date'));
        $endDate = Carbon::createFromFormat('Y-m-d', $request->input('end_date'));

        // Generate an array with all dates between startDate and endDate
        $dateRange = new DatePeriod(
            $startDate,
            new DateInterval('P1D'),
            $endDate->addDay() // Add one day to include the end date in the period
        );

        // Convert the date range to an array of formatted strings
        $dates = [];
        foreach ($dateRange as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        // Query the database for sales data
        $salesData = Sale::where('sales.company_id', session('company_id'))->whereBetween('sale_date', [$startDate, $endDate])
            ->groupBy('date')
            ->selectRaw('DATE(sale_date) as date, SUM(total) as sales_total, COUNT(*) as sales_count')
            ->get()
            ->keyBy('date'); // Key the collection by the date

        $activeCustomer = Sale::where('sales.company_id', session('company_id'))->select('customer_id', DB::raw('COUNT(*) as number_of_sales'), DB::raw('SUM(total) as total_sales'))
            ->with(['customer' => function ($query) {
                $query->select('id', 'first_name');
            }])
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->groupBy('customer_id')
            ->orderBy('total_sales', 'desc') // Order by total_sales in descending order
            ->limit(5)
            ->get();

        // $mostPopularProducts = ProductTransaction::select(
        //     'product_id',
        //     DB::raw('SUM(quantity) as total_quantity'),
        //     DB::raw('SUM(quantity * unit_price) as total_sales')
        // )
        //     ->join('sales', 'product_transactions.sale_id', '=', 'sales.id')
        //     ->join('products', 'product_transactions.product_id', '=', 'products.id')
        //     ->whereBetween('sales.sale_date', [$startDate, $endDate])
        //     ->groupBy('product_id')
        //     ->orderBy('total_quantity', 'desc')
        //     ->limit(5)
        //     ->get();

        // $mostPopularProductsData = $mostPopularProducts->map(function ($product) {
        //     return [
        //         'product_name' => $product->product->name ?? 'Untitled',
        //         'total_quantity' => $product->total_quantity,
        //         'total_sales' => $product->total_sales,
        //         'details_url' => "/reports/products/{$product->product_id}/details"
        //     ];
        // });

        $mostPopularProducts = SaleProduct::where('sale_products.company_id', session('company_id'))->select(
            'product_name',
            'sale_products.shopify_id',
            'sale_products.shopify_product_id',
            'sale_products.shopify_variants_id',
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(quantity * unit_price) as total_sales')
        )
            ->join('sales', 'sale_products.sale_id', '=', 'sales.id')
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->groupBy('product_name', 'shopify_id', 'shopify_variants_id', 'shopify_product_id')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get();

        $mostPopularProductsData = $mostPopularProducts->map(function ($saleProduct) {
            return [
                'product_name' => $saleProduct->product_name ?? '-',
                'total_quantity' => $saleProduct->total_quantity,
                'total_sales' => $saleProduct->total_sales,
                'shopify_id' => $saleProduct->shopify_id,
                'shopify_variants_id' => $saleProduct->shopify_variants_id
            ];
        });

        // Ensure all dates are included in the results, even if there are no sales
        $formattedData = collect($dates)->mapWithKeys(function ($date) use ($salesData) {
            return [
                $date => [
                    'date' => $date,
                    'sales_total' => $salesData->has($date) ? $salesData[$date]['sales_total'] : 0,
                    'sales_count' => $salesData->has($date) ? $salesData[$date]['sales_count'] : 0,
                ]
            ];
        });

        $responseDate = array();
        $responseDate["table_data"] = $formattedData->values()->all();
        $responseDate['active_customers'] = $activeCustomer;
        $responseDate['most_popular_products'] = $mostPopularProductsData;

        // Return the data as a JSON response
        return response()->json($responseDate);
    }

    public function getMaterialsData(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Retrieve sales data between start_date and end_date
        $startDate = Carbon::createFromFormat('Y-m-d', $request->input('start_date'));
        $endDate = Carbon::createFromFormat('Y-m-d', $request->input('end_date'));

        // Generate an array with all dates between startDate and endDate
        $dateRange = new DatePeriod(
            $startDate,
            new DateInterval('P1D'),
            $endDate->addDay() // Add one day to include the end date in the period
        );

        // Convert the date range to an array of formatted strings
        $dates = [];
        foreach ($dateRange as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        $inventoryVolume = MaterialTransaction::where('material_transactions.company_id', session('company_id'))->select(
            DB::raw('SUM(CASE WHEN transaction_type = 1 THEN quantity ELSE 0 END) as total_incoming'),
            DB::raw('SUM(CASE WHEN transaction_type = 2 THEN quantity ELSE 0 END) as total_outgoing')
        )
            ->where('created_at', '<', $startDate)
            ->get()
            ->map(function ($transaction) {
                return $transaction->total_incoming - $transaction->total_outgoing;
            })
            ->sum();

        // Query the database for sales data
        $materialData = MaterialTransaction::where('material_transactions.company_id', session('company_id'))->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->selectRaw('DATE(created_at) as date, 
                SUM(CASE WHEN transaction_type = 1 THEN quantity * unit_price
                WHEN transaction_type = 2 THEN -quantity * unit_price
                ELSE 0 END) as material_total, 
                COUNT(*) as material_count')
            ->get()
            ->keyBy('date');

        $formattedInventoryValue = collect($dates)->mapWithKeys(function ($date) use ($materialData) {
            return [
                $date => [
                    'date' => $date,
                    'material_total' => $materialData->has($date) ? $materialData[$date]['material_total'] : 0,
                    'material_count' => $materialData->has($date) ? $materialData[$date]['material_count'] : 0,
                ]
            ];
        });


        $materialPurchasedData = MaterialTransaction::where('material_transactions.company_id', session('company_id'))->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('order_id') // Add this line to check for non-null order_id
            ->groupBy('date')
            ->selectRaw('DATE(created_at) as date, 
                SUM(quantity*unit_price) as material_total,
                COUNT(*) as material_count')
            ->get()
            ->keyBy('date');

        $formattedMaterialPurchasedData = collect($dates)->mapWithKeys(function ($date) use ($materialPurchasedData) {
            return [
                $date => [
                    'date' => $date,
                    'material_total' => $materialPurchasedData->has($date) ? $materialPurchasedData[$date]['material_total'] : 0,
                    'material_count' => $materialPurchasedData->has($date) ? $materialPurchasedData[$date]['material_count'] : 0,
                ]
            ];
        });

        $materialProducedData = MaterialTransaction::where('material_transactions.company_id', session('company_id'))->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('user_id')
            ->groupBy('date')
            ->selectRaw('DATE(created_at) as date, 
                SUM(quantity*unit_price) as material_total,
                COUNT(*) as material_count')
            ->get()
            ->keyBy('date');

        $formattedMaterialProducedData = collect($dates)->mapWithKeys(function ($date) use ($materialProducedData) {
            return [
                $date => [
                    'date' => $date,
                    'material_total' => $materialProducedData->has($date) ? $materialProducedData[$date]['material_total'] : 0,
                    'material_count' => $materialProducedData->has($date) ? $materialProducedData[$date]['material_count'] : 0,
                ]
            ];
        });

        $materialUsedData = MaterialTransaction::where('material_transactions.company_id', session('company_id'))->whereBetween('created_at', [$startDate, $endDate])
            ->where('transaction_type', 2)
            ->groupBy('date')
            ->selectRaw('DATE(created_at) as date, 
            SUM(quantity*unit_price) as material_total,
            COUNT(*) as material_count')
            ->get()
            ->keyBy('date');

        $formattedMaterialUsedData = collect($dates)->mapWithKeys(function ($date) use ($materialUsedData) {
            return [
                $date => [
                    'date' => $date,
                    'material_total' => $materialUsedData->has($date) ? $materialUsedData[$date]['material_total'] : 0,
                    'material_count' => $materialUsedData->has($date) ? $materialUsedData[$date]['material_count'] : 0,
                ]
            ];
        });

        $topMaterialsAdded = MaterialTransaction::where('material_transactions.company_id', session('company_id'))->whereBetween('material_transactions.created_at', [$startDate, $endDate])
            ->where('transaction_type', 1)
            ->join('materials', 'material_transactions.material_id', '=', 'materials.id')
            ->join('units', 'materials.unit_id', '=', 'units.id')
            ->select(
                'material_transactions.material_id',
                'materials.name as material_name',
                'units.short_name as unit_name',
                DB::raw('SUM(material_transactions.quantity) as total_quantity'),
                DB::raw('SUM(material_transactions.quantity * material_transactions.unit_price) as total_costs')
            )
            ->groupBy('material_transactions.material_id', 'materials.name', 'units.short_name') // Include all selected columns in GROUP BY
            ->orderBy('total_costs', 'desc')
            ->limit(5)
            ->get();

        $slowMovingMaterials = MaterialTransaction::where('material_transactions.company_id', session('company_id'))->whereBetween('material_transactions.created_at', [$startDate, $endDate])
            ->where('transaction_type', 2)
            ->join('materials', 'material_transactions.material_id', '=', 'materials.id')
            ->join('units', 'materials.unit_id', '=', 'units.id')
            ->select(
                'material_transactions.material_id',
                'materials.name as material_name',
                'units.short_name as unit_name',
                DB::raw('SUM(material_transactions.quantity) as total_quantity'),
                DB::raw('SUM(material_transactions.quantity * material_transactions.unit_price) as total_costs')
            )
            ->groupBy('material_transactions.material_id', 'materials.name', 'units.short_name') // Include all selected columns in GROUP BY
            ->orderBy('total_costs', 'desc')
            ->limit(5)
            ->get();

        $responseDate = array();
        $responseDate["inventory_value"] = $formattedInventoryValue->values()->all();
        $responseDate["material_purchased"] = $formattedMaterialPurchasedData->values()->all();
        $responseDate["material_produced"] = $formattedMaterialProducedData->values()->all();
        $responseDate["material_used"] = $formattedMaterialUsedData->values()->all();

        $responseDate['top_materials'] = $topMaterialsAdded;
        $responseDate['slow_materials'] = $slowMovingMaterials;


        $responseDate["inventoryVolume"] = $inventoryVolume;


        // Return the data as a JSON response
        return response()->json($responseDate);
    }

    public function getProductsData(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Retrieve sales data between start_date and end_date
        $startDate = Carbon::createFromFormat('Y-m-d', $request->input('start_date'));
        $endDate = Carbon::createFromFormat('Y-m-d', $request->input('end_date'));

        // Generate an array with all dates between startDate and endDate
        $dateRange = new DatePeriod(
            $startDate,
            new DateInterval('P1D'),
            $endDate->addDay() // Add one day to include the end date in the period
        );

        // Convert the date range to an array of formatted strings
        $dates = [];
        foreach ($dateRange as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        $inventoryVolume = ProductTransaction::where('product_transactions.company_id', session('company_id'))->select(
            DB::raw('SUM(CASE WHEN transaction_type = 1 THEN quantity ELSE 0 END) as total_incoming'),
            DB::raw('SUM(CASE WHEN transaction_type = 2 THEN quantity ELSE 0 END) as total_outgoing')
        )
            ->where('created_at', '<', $startDate)
            ->get()
            ->map(function ($transaction) {
                return $transaction->total_incoming - $transaction->total_outgoing;
            })
            ->sum();

        // Query the database for sales data
        $productData = ProductTransaction::where('product_transactions.company_id', session('company_id'))->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->selectRaw('DATE(created_at) as date, 
                SUM(CASE WHEN transaction_type = 1 THEN quantity * unit_price
                WHEN transaction_type = 2 THEN -quantity * unit_price 
                ELSE 0 END) as product_total, 
                COUNT(*) as product_count')
            ->get()
            ->keyBy('date');

        $formattedInventoryValue = collect($dates)->mapWithKeys(function ($date) use ($productData) {
            return [
                $date => [
                    'date' => $date,
                    'product_total' => $productData->has($date) ? $productData[$date]['product_total'] : 0,
                    'product_count' => $productData->has($date) ? $productData[$date]['product_count'] : 0,
                ]
            ];
        });


        $productPurchasedData = ProductTransaction::where('product_transactions.company_id', session('company_id'))->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('production_id') // Add this line to check for non-null order_id
            ->groupBy('date')
            ->selectRaw('DATE(created_at) as date, 
                SUM(quantity*unit_price) as product_total,
                COUNT(*) as product_count')
            ->get()
            ->keyBy('date');

        $formattedProductPurchasedData = collect($dates)->mapWithKeys(function ($date) use ($productPurchasedData) {
            return [
                $date => [
                    'date' => $date,
                    'product_total' => $productPurchasedData->has($date) ? $productPurchasedData[$date]['product_total'] : 0,
                    'product_count' => $productPurchasedData->has($date) ? $productPurchasedData[$date]['product_count'] : 0,
                ]
            ];
        });

        $productProducedData = ProductTransaction::where('product_transactions.company_id', session('company_id'))->whereBetween('created_at', [$startDate, $endDate])
            ->where('transaction_type', 1)
            ->groupBy('date')
            ->selectRaw('DATE(created_at) as date, 
                SUM(quantity*unit_price) as product_total,
                COUNT(*) as product_count')
            ->get()
            ->keyBy('date');

        $formattedProductProducedData = collect($dates)->mapWithKeys(function ($date) use ($productProducedData) {
            return [
                $date => [
                    'date' => $date,
                    'product_total' => $productProducedData->has($date) ? $productProducedData[$date]['product_total'] : 0,
                    'product_count' => $productProducedData->has($date) ? $productProducedData[$date]['product_count'] : 0,
                ]
            ];
        });

        $productUsedData = ProductTransaction::where('product_transactions.company_id', session('company_id'))->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('sale_id')
            ->groupBy('date')
            ->selectRaw('DATE(created_at) as date, 
            SUM(quantity*unit_price) as product_total,
            COUNT(*) as product_count')
            ->get()
            ->keyBy('date');

        $formattedProductUsedData = collect($dates)->mapWithKeys(function ($date) use ($productUsedData) {
            return [
                $date => [
                    'date' => $date,
                    'product_total' => $productUsedData->has($date) ? $productUsedData[$date]['product_total'] : 0,
                    'product_count' => $productUsedData->has($date) ? $productUsedData[$date]['product_count'] : 0,
                ]
            ];
        });

        $topProductsAdded = ProductTransaction::where('product_transactions.company_id', session('company_id'))->whereBetween('product_transactions.created_at', [$startDate, $endDate])
            ->where('transaction_type', 1)
            ->join('products', 'product_transactions.product_id', '=', 'products.id')
            ->join('units', 'products.unit_id', '=', 'units.id')
            ->select(
                'product_transactions.product_id',
                'products.name as product_name',
                'units.short_name as unit_name',
                DB::raw('SUM(product_transactions.quantity) as total_quantity'),
                DB::raw('SUM(product_transactions.quantity * product_transactions.unit_price) as total_costs')
            )
            ->groupBy('product_transactions.product_id', 'products.name', 'units.short_name') // Include all selected columns in GROUP BY
            ->orderBy('total_costs', 'desc')
            ->limit(5)
            ->get();

        $slowMovingProducts = ProductTransaction::where('product_transactions.company_id', session('company_id'))->whereBetween('product_transactions.created_at', [$startDate, $endDate])
            ->where('transaction_type', 2)
            ->join('products', 'product_transactions.product_id', '=', 'products.id')
            ->join('units', 'products.unit_id', '=', 'units.id')
            ->select(
                'product_transactions.product_id',
                'products.name as product_name',
                'units.short_name as unit_name',
                DB::raw('SUM(product_transactions.quantity) as total_quantity'),
                DB::raw('SUM(product_transactions.quantity * product_transactions.unit_price) as total_costs')
            )
            ->groupBy('product_transactions.product_id', 'products.name', 'units.short_name') // Include all selected columns in GROUP BY
            ->orderBy('total_costs', 'desc')
            ->limit(5)
            ->get();

        $responseDate = array();
        $responseDate["inventory_value"] = $formattedInventoryValue->values()->all();
        $responseDate["product_purchased"] = $formattedProductPurchasedData->values()->all();
        $responseDate["product_produced"] = $formattedProductProducedData->values()->all();
        $responseDate["product_used"] = $formattedProductUsedData->values()->all();

        $responseDate['top_products'] = $topProductsAdded;
        $responseDate['slow_products'] = $slowMovingProducts;


        $responseDate["inventoryVolume"] = $inventoryVolume;


        // Return the data as a JSON response
        return response()->json($responseDate);
    }


    public function getInventoryBreakdownData(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
        ]);

        // Retrieve sales data between start_date and end_date
        $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $request->input('start_date'));
        $responseDate = array();
        $materialCategories = Category::where('company_id', session('company_id'))->where('type', 1)->get();
        foreach ($materialCategories as $category) {

            $materials = Material::where('materials.company_id', session('company_id'))->where('category_id', $category->id)
                ->whereHas('materialTransactions', function ($query) use ($startDate) {
                    $query->where('created_at', '<', $startDate);
                })
                ->with(['materialTransactions' => function ($query) {
                    $query->selectRaw('
                        material_id,
                        SUM(CASE WHEN transaction_type = 1 THEN quantity * unit_price
                        WHEN transaction_type = 2 THEN -quantity * unit_price
                        ELSE 0 END) as total_cost
                        ')
                        ->groupBy('material_id');
                }])
                ->get()
                ->map(function ($material) {
                    return [
                        'id' => $material->id,
                        'name' => $material->name,
                        'total_cost' => $material->materialTransactions->sum('total_cost')
                    ];
                });
            $responseDate["materials"][$category->name] = $materials;
        }

        $productCategories = Category::where('company_id', session('company_id'))->where('type', 2)->get();

        foreach ($productCategories as $category) {

            $products = Product::where('products.company_id', session('company_id'))->where('category_id', $category->id)
                ->whereHas('productTransactions', function ($query) use ($startDate) {
                    $query->where('created_at', '<', $startDate);
                })
                ->with(['productTransactions' => function ($query) {
                    $query->selectRaw('
                        product_id,
                        SUM(CASE WHEN transaction_type = 1 THEN quantity * unit_price
                        WHEN transaction_type = 2 THEN -quantity * unit_price
                        ELSE 0 END) as total_cost
                        ')
                        ->groupBy('product_id');
                }])
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'total_cost' => $product->productTransactions->sum('total_cost')
                    ];
                });
            $responseDate["products"][$category->name] = $products;
        }
        // Return the data as a JSON response
        return response()->json($responseDate);
    }
}
