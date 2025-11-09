<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;

class ShippedPackageController extends Controller
{
    public function index()
    {
        $shipments = Shipment::orderBy('created_at', 'desc')->paginate(20);
        return view('shipping.shipped_packages', compact('shipments'));
    }
}
