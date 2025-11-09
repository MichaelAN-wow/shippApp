<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductTransaction;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Material;
use App\Models\Unit;
use App\Models\Sale;
use App\Models\SaleProduct;
use App\Models\Company;
use Carbon\Carbon;

class SaleController extends Controller
{
    public function store(Request $request)
    {
        $data = new Sale;

        $data->customer_id = $request->customer_id;
        $data->sale_type = $request->saleType;
        $data->notes = $request->notes;
        $data->total = str_replace('$', '', $request->total);
        $data->company_id = session('company_id');

        $data->save();
        $saleId = $data->id;
        $productIds = $request->product_id;
        $productCounts = $request->product_quantity;
        $unitPrices = $request->unit_price;
        $size = $productIds !== null ? count($productIds) : 0;

        for ($i = 0; $i < $size; $i++) {
            $orderMaterial = new SaleProduct;
            $orderMaterial->sale_id = $saleId;
            $orderMaterial->product_id = $productIds[$i];
            $orderMaterial->quantity = $productCounts[$i];
            $orderMaterial->unit_price = str_replace('$', '', $unitPrices[$i]);
            $orderMaterial->company_id = session('company_id');
            $orderMaterial->save();
        }

        return Redirect()->route('all.sales');
    }

    public function destroy($id)
    {
        $sale = Sale::find($id);
        if ($sale) {
            $sale->delete();
            ProductTransaction::where('sale_id', $id)->delete();
            return response()->json(['success' => true, 'message' => 'Order deleted successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Order not found.']);
        }
    }

    public function getAllData($type = null)
    {
        $products = Product::where('company_id', session('company_id'))->with('unit')->orderBy('name', 'asc')->get();
        $suppliers = Supplier::where('company_id', session('company_id'))->get();
        $customers = Customer::where('company_id', session('company_id'))->get();

        $limit = 50;

        switch ($type) {
            case 1:
                $sales = Sale::where('company_id', session('company_id'))->where('sale_type', 1)->orderBy('sale_date', 'desc')->paginate($limit);
                $categoryId = 1;
                break;
            case 2:
                $sales = Sale::where('company_id', session('company_id'))->where('sale_type', 2)->orderBy('sale_date', 'desc')->paginate($limit);
                $categoryId = 2;
                break;
            case 3:
                $sales = Sale::where('company_id', session('company_id'))->where('sale_type', 3)->orderBy('sale_date', 'desc')->paginate($limit);
                $categoryId = 3;
                break;
            default:
                $sales = Sale::where('company_id', session('company_id'))->orderBy('sale_date', 'desc')->paginate($limit);
                $categoryId = 0;
        }

        $companyId = session('company_id');
        $company = Company::find($companyId);
        $yearly_goal = $company ? $company->yearly_goal : null;

        $currentYear = now()->year;
        $currentMonth = now()->month;
        $totalSalesThisYear = Sale::where('company_id', $companyId)
            ->whereYear('sale_date', $currentYear)
            ->sum('total');

        $today = now();
        $startOfYear = now()->startOfYear();
        $endOfYear = now()->endOfYear();
        $daysPassed = $today->diffInDays($startOfYear);
        $daysRemaining = $endOfYear->diffInDays($today) + 1;

        $daily_goal = $yearly_goal !== null && $daysRemaining > 0
            ? round(max($yearly_goal - $totalSalesThisYear, 0) / $daysRemaining)
            : null;

        $monthlyAverage = $currentMonth > 0 ? $totalSalesThisYear / $currentMonth : 0;
        $projectedTotal = $monthlyAverage * 12;
        $gap = max(0, $yearly_goal - $projectedTotal);

        return view('Admin.all_sales', compact(
            'sales',
            'customers',
            'products',
            'categoryId',
            'yearly_goal',
            'daily_goal',
            'totalSalesThisYear',
            'monthlyAverage',
            'projectedTotal',
            'gap'
        ));
    }

    public function getRetailData()
    {
        return $this->getAllData(1);
    }

    public function getWholeSaleData()
    {
        return $this->getAllData(2);
    }

    public function getShopifyData()
    {
        return $this->getAllData(3);
    }

    public function getPageScroll(Request $request)
    {
        if ($request->ajax()) {
            $limit = 50;
            $categoryId = (int) $request->query('categoryId');

            $sales = $categoryId == 0
                ? Sale::where('company_id', session('company_id'))->orderBy('sale_date', 'desc')->paginate($limit)
                : Sale::where('company_id', session('company_id'))->where('sale_type', $categoryId)->orderBy('sale_date', 'desc')->paginate($limit);

            return view('Admin.sales_table', compact('sales'))->render();
        }
    }

    public function show($id)
    {
        $sale = Sale::findOrFail($id);
        $sale->customer = Customer::find($sale->customer_id);
        $sale->items = SaleProduct::where('sale_id', $sale->id)->get()->map(function ($saleProduct) {
            $saleProduct->product = Product::find($saleProduct->product_id);
            return $saleProduct;
        });

        return response()->json($sale);
    }

    public function edit($id)
    {
        $sale = Sale::findOrFail($id);
        $customer = Customer::find($sale->customer_id);
        $items = SaleProduct::where('sale_id', $sale->id)->get()->map(function ($saleProduct) {
            $saleProduct->product = Product::find($saleProduct->product_id);
            return $saleProduct;
        });

        return response()->json([
            'sale' => $sale,
            'customer' => $customer,
            'items' => $items,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'saleType' => 'required|in:1,2',
            'sale_date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'discount' => 'nullable|numeric',
            'tax' => 'nullable|numeric',
            'total' => 'required|numeric',
            'notes' => 'nullable|string|max:255',
            'product_id.*' => 'required|exists:products,id',
            'unit_price.*' => 'required|numeric',
            'product_quantity.*' => 'required|numeric',
            'sub_total.*' => 'required|numeric',
        ]);

        $sale = Sale::findOrFail($id);
        $sale->sale_type = $validatedData['saleType'];
        $sale->sale_date = $validatedData['sale_date'];
        $sale->customer_id = $validatedData['customer_id'];
        $sale->discount = $validatedData['discount'];
        $sale->tax = $validatedData['tax'];
        $sale->total = $validatedData['total'];
        $sale->notes = $validatedData['notes'];
        $sale->save();

        SaleProduct::where('sale_id', $sale->id)->delete();

        foreach ($validatedData['product_id'] as $index => $productId) {
            SaleProduct::create([
                'sale_id' => $sale->id,
                'product_id' => $productId,
                'unit_price' => $validatedData['unit_price'][$index],
                'quantity' => $validatedData['product_quantity'][$index],
            ]);
        }

        return redirect()->route('all.sales')->with('success', 'Sale updated successfully.');
    }

    public function updateYearlyTarget(Request $request)
    {
        $request->validate([
            'yearly_target' => 'required|integer|min:0',
        ]);

        $companyId = session('company_id');

        if (!$companyId) {
            return back()->with('error', 'No company selected.');
        }

        try {
            $company = Company::find($companyId);

            if (!$company) {
                return back()->with('error', 'Company not found.');
            }

            $company->yearly_goal = $request->yearly_target;
            $company->save();

            return back()->with('success', 'Yearly target updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
