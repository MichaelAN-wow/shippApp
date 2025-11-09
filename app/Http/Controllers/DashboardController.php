<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\Box;
use App\Models\Contact;

class DashboardController extends Controller
{
    public function index()
    {
        $openOrders = Order::where('status', 1)->get(); 
        $recentShipments = Shipment::orderBy('created_at', 'desc')->take(10)->get();
        $boxes = Box::all();
        $contacts = Contact::orderBy('name')->get();

        $stats = [
            'pending_orders' => $openOrders->count(),
            'shipped_orders' => Shipment::where('status', 'shipped')->count(),
            'in_transit' => Shipment::where('status', 'in_transit')->count(),
            'delivered' => Shipment::where('status', 'delivered')->count(),
        ];

        return view('shipping.dashboard', compact('openOrders', 'recentShipments', 'boxes', 'contacts', 'stats'));
    }
}
