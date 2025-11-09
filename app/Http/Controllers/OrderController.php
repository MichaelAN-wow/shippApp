<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Material;
use App\Models\MaterialTransaction;
use App\Models\Unit;
use App\Models\OrderMaterial;
use App\Models\ProductMaterial;

class OrderController extends Controller
{

    public function store(Request $request)
    {
        $data = new Order;

        $data->supplier_id = $request->supplier_id;
        $data->status = $request->status;
        $data->order_number = $request->order_number;
        $data->notes = $request->notes;
        $data->discount = $request->discount;
        $data->tax = $request->tax;
        $data->shipping = $request->shipping;
        $data->items = count($request->material_id);

        $totalPrice = str_replace('$', '', $request->total);
        $data->total = str_replace(',', '', $totalPrice);
        $data->received_at = $request->received_at;
        
        $data->company_id = session('company_id');
        $data->save();
        $orderId = $data->id;
        $materialIds = $request->material_id;
        $material_unit_id = $request->material_unit_id;
        $materialCounts = $request->material_count;
        $materialUnitFactors = $request->material_unit_factor;
        $materialPrices = $request->unit_price;

        $msg = 'A new order has been successfully created!';
        for ($i = 0; $i < count($materialIds); $i++) {
            $orderMaterial = new OrderMaterial;
            $orderMaterial->order_id = $orderId;
            $orderMaterial->material_id = $materialIds[$i];
            $orderMaterial->unit_id = $material_unit_id[$i];
            $orderMaterial->quantity = $materialCounts[$i];
            $orderMaterial->unit_price = str_replace('$', '', $materialPrices[$i]);
            $orderMaterial->company_id = session('company_id');
            $orderMaterial->save();

            if ($data->status == 2) { // only update material info when it is received
                $msg = 'A new order has been created, and the materials stock level has been updated.';
                $material = Material::find($materialIds[$i]);

                $updatedStockLevel = Material::updateMaterialInfo(
                    $materialIds[$i],
                    str_replace('$', '', $materialPrices[$i]),
                    $materialCounts[$i] * $materialUnitFactors[$i]
                );
                
                if ($material->material_base) {
                    // Handle material base case
                    $productMaterials = ProductMaterial::where('product_id', $material->id)
                        ->where('product_id_material_base', $material->id)
                        ->with(['material.unit'])
                        ->get();

                    foreach ($productMaterials as $productMaterial) {
                        // Calculate the quantity needed for each component material
                        $componentQuantity = $materialCounts[$i] * $productMaterial->used_amount;
                        
                        // Update stock level for component material
                        $updatedStockLevel = Material::updateMaterialInfo(
                            $productMaterial->material_id,
                            $productMaterial->unit_price,
                            -$componentQuantity // Negative because we're deducting
                        );

                        if ($updatedStockLevel !== null) {
                            // Create transaction record for component material
                            $materialTransaction = new MaterialTransaction;
                            $materialTransaction->order_id = $orderId;
                            $materialTransaction->transaction_type = 2; // export option
                            $materialTransaction->material_id = $productMaterial->material_id;
                            $materialTransaction->quantity = $componentQuantity;
                            $materialTransaction->before_level = $updatedStockLevel + $componentQuantity;
                            $materialTransaction->to_level = $updatedStockLevel;
                            $materialTransaction->unit_price = $productMaterial->unit_price;
                            $materialTransaction->company_id = session('company_id');
                            $materialTransaction->save();
                        }

                        $updatedStockLevel = Material::updateMaterialInfo(
                            $materialIds[$i],
                            str_replace('$', '', $materialPrices[$i]),
                            $materialCounts[$i] * $materialUnitFactors[$i]
                        );
                    }
                } else {
                    // Handle regular material case
                    $updatedStockLevel = Material::updateMaterialInfo(
                        $materialIds[$i],
                        str_replace('$', '', $materialPrices[$i]),
                        $materialCounts[$i] * $materialUnitFactors[$i]
                    );

                    if ($updatedStockLevel !== null) {
                        $materialTransaction = new MaterialTransaction;
                        $materialTransaction->order_id = $orderId;
                        $materialTransaction->transaction_type = 1; // import option
                        $materialTransaction->material_id = $materialIds[$i];
                        $materialTransaction->quantity = $materialCounts[$i] * $materialUnitFactors[$i];
                        $materialTransaction->before_level = $updatedStockLevel - $materialCounts[$i] * $materialUnitFactors[$i];
                        $materialTransaction->to_level = $updatedStockLevel;
                        $materialTransaction->unit_price = str_replace('$', '', $materialPrices[$i]);
                        $materialTransaction->company_id = session('company_id');
                        $materialTransaction->save();
                    }
                }
            }
        }
        return Redirect()->route('all.orders')->with('success', $msg);
    }
    public function newStore(Request $request)
    {

        $data = new Order;
        $data->email = $request->email;

        $data->product_code = $request->code;
        $data->product_name = $request->name;
        $data->quantity = $request->quantity;
        $data->order_status = 0;
        $data->company_id = session('company_id');
        $data->save();

        //customer_track
        $customer = Customer::where('email', '=', $request->email)->first();
        if ($customer === null) {
            $data3 = new Customer;
            $data3->name = $request->name;
            $data3->email = $request->email;
            $data3->company = $request->company;
            $data3->address = $request->address;
            $data3->phone = $request->phone;
            $data3->company_id = session('company_id');
            $data3->save();
        }
        return Redirect()->route('all.orders');
    }

    public function create_purchase_from_alert(Request $request)
    {
        // Create a new Order entry
        $data = new Order;
        $data->supplier_id = $request->supplier_id ?? $request->materials[0]['supplier_id'];
        $data->status = 0;
        $data->order_number = time();
        $data->notes = 'Generated from stock alerts';
        $data->discount = 0;
        $data->tax = 0;
        $data->shipping = 0;
        $data->items = count($request->materials);

        // Calculate the total price
        $totalPrice = 0;
        foreach ($request->materials as $material) {
            $requiredQuantity = $material['min_stock_level'] - $material['current_stock_level'];
            $totalPrice += $requiredQuantity * str_replace(',', '', $material['price_per_unit']);
        }
        $data->total = $totalPrice;

        $data->company_id = session('company_id');
        $data->save();

        // Get the generated order ID
        $orderId = $data->id;

        // Process each material and create OrderMaterial entries
        foreach ($request->materials as $material) {
            $requiredQuantity = $material['min_stock_level'] - $material['current_stock_level'];
            $materialUnitFactor = $material['unit']['conversion_factor'] ?? 1;

            // Create the OrderMaterial entry
            $orderMaterial = new OrderMaterial;
            $orderMaterial->order_id = $orderId;
            $orderMaterial->material_id = $material['id'];
            $orderMaterial->unit_id = $material['unit_id'];
            $orderMaterial->quantity = $requiredQuantity;
            $orderMaterial->unit_price = str_replace(',', '', $material['price_per_unit']);
            $orderMaterial->company_id = session('company_id');
            $orderMaterial->save();
        }

        return response()->json(['success' => true, 'message' => 'Purchase order created from alert successfully']);
    }

    public function getAllOrders(Request $request)
    {
        $limit = $request->input('limit', 100);
        $orders = Order::where('company_id', session('company_id'))->orderBy('created_at', 'desc')->get();
        $suppliers = Supplier::where('company_id', session('company_id'))->get();
        $units = Unit::all()->groupBy('type');
        $materials = Material::where('company_id', session('company_id'))->with('unit')->orderBy('name', 'asc')->get();

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
        return view('Admin.all_orders', compact('orders', 'units', 'suppliers', 'materials', 'unitsJson'));
    }

    public function getOrderDetails($id)
    {
        $order = Order::where('company_id', session('company_id'))->with(['supplier', 'orderMaterials.material'])->findOrFail($id);
        $units = Unit::all();
        return response()->json([
            'order' => $order,
            'units' => $units
        ]);
    }

    public function destroy($id)
    {
        $order = Order::find($id);
        if ($order) {
            // Delete related order materials
            $order->orderMaterials()->delete();

            // Delete the order itself
            $order->delete();

            // Optionally delete related material transactions if they exist
            MaterialTransaction::where('order_id', $id)->delete();

            return response()->json(['success' => true, 'message' => 'Order deleted successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Order not found.']);
        }
    }

    public function getPageScroll(Request $request)
    {
        if ($request->ajax()) {

            $limit = 100; // Default to 10 if no limit is set
            $orders = Order::where('company_id', session('company_id'))->paginate($limit);
            return view('Admin.orders_table', compact('orders'))->render();
        }
    }

    public function update(Request $request, $id)
    {

        $request->merge([
            'total' => preg_replace('/[^\d.-]/', '', $request->total),
            'sub_total' => array_map(function ($value) {
                return preg_replace('/[^\d.-]/', '', $value);
            }, $request->sub_total),
        ]);

        $msg = 'Order updated successfully';

        $validatedData = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'status' => 'required|in:0,1,2',
            'received_at' => 'nullable|date',
            'discount' => 'nullable|numeric',
            'tax' => 'nullable|numeric',
            'shipping' => 'nullable|numeric',
            'total' => 'required|numeric',
            'order_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:255',
            'material_id.*' => 'required|exists:materials,id',
            'unit_price.*' => 'required|numeric',
            'material_count.*' => 'required|numeric',
            'sub_total.*' => 'required|numeric',
        ]);

        $order = Order::findOrFail($id);
        $isUpdateMaterialInfoEnabled = false;
        if ($order->status !== $request->status && $request->status == 2) {
            $isUpdateMaterialInfoEnabled = true; //if the order status has changed to `Received`
            $msg = 'Order updated successfully and material stock levels have been updated';
        }
        $order->update([
            'supplier_id' => $request->supplier_id,
            'status' => $request->status,
            'received_at' => $request->received_at,
            'discount' => $request->discount,
            'tax' => $request->tax,
            'shipping' => $request->shipping,
            'total' => $request->total,
            'order_number' => $request->order_number,
            'notes' => $request->notes,
            'company_id' => session('company_id'),
        ]);

        $materialUnitFactors = $request->material_unit_factor;
        $material_unit_id = $request->material_unit_id;

        // Sync order materials
        $order->orderMaterials()->delete(); // Delete existing materials

        foreach ($request->material_id as $index => $materialId) {
            $order->orderMaterials()->create([
                'material_id' => $materialId,
                'unit_id' => $material_unit_id[$index],
                'unit_price' => $request->unit_price[$index],
                'quantity' => $request->material_count[$index] * $materialUnitFactors[$index],
                'company_id' => session('company_id'),
            ]);

            if ($isUpdateMaterialInfoEnabled) {
                $updatedStockLevel = Material::updateMaterialInfo($materialId, str_replace('$', '', $request->unit_price[$index]), $request->material_count[$index] * $materialUnitFactors[$index]); //update current stock level

                if ($updatedStockLevel !== null) {
                    $materialTransaction = new MaterialTransaction;
                    $materialTransaction->order_id = $order->id;
                    $materialTransaction->transaction_type = 1; //import option
                    $materialTransaction->material_id = $materialId;
                    $materialTransaction->quantity = $request->material_count[$index] * $materialUnitFactors[$index];
                    $materialTransaction->before_level = $updatedStockLevel - $request->material_count[$index] * $materialUnitFactors[$index];
                    $materialTransaction->to_level = $updatedStockLevel;
                    $materialTransaction->unit_price = str_replace('$', '', $request->unit_price[$index]);
                    $materialTransaction->company_id = session('company_id');
                    $materialTransaction->save();
                }
            }
        }

        return redirect()->route('all.orders')->with('success', $msg);
    }
    
}
