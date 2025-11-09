<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Order;
use App\Models\OrderMaterial;


class SupplierController extends Controller
{
    public function store(Request $request)
    {

        $data = new Supplier;

        $data->name = $request->name;
        $data->website = $request->website;
        $data->contact_name = $request->contact_name;
        $data->phone = $request->phone;
        $data->email = $request->email;
        $data->notes = $request->notes;
        $data->company_id = session('company_id');

        $data->save();
        return Redirect()->route('all.suppliers');
    }


    public function index()
    {

        $suppliers = Supplier::where('company_id', session('company_id'))->get();

        foreach ($suppliers as $supplier) {
            $orders = Order::where('supplier_id', $supplier->id)->get();
            $supplier->orders_count = $orders->count();

            $totalSpent = 0;
            foreach ($orders as $order) {
                $orderMaterials = OrderMaterial::where('order_id', $order->id)->get();
                $order->orderMaterials = $orderMaterials;

                foreach ($orderMaterials as $material) {
                    $totalSpent += $material->quantity * $material->unit_price;
                }
            }

            $supplier->total_spent = $totalSpent;
            $supplier->orders = $orders;
        }

        return view('Admin.all_suppliers', compact('suppliers'));
    }

    public function getSupplierOrders($id)
    {
        $supplier = Supplier::findOrFail($id);

        // Fetch orders with materials
        $orders = Order::where('supplier_id', $id)->with('orderMaterials.material')->get();

        // Structure the data manually
        $supplier->orders = $orders;

        return response()->json([
            'supplier' => $supplier,
        ]);
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);

        return response()->json($supplier);
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return response()->json(['success' => true, 'message' => 'Supplier deleted successfully.']);
    }
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'website' => 'nullable|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $supplier = Supplier::findOrFail($id);
        $supplier->update($validatedData);

        return redirect()->route('all.suppliers')->with('success', 'Supplier updated successfully');
    }
}
