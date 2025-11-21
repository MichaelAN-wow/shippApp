<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\Box;
use App\Models\Contact;
use App\Models\Customer;

class DashboardController extends Controller
{
    public function index()
    {
        // Open Orders
        $openOrders = Shipment::with('receiver')
            ->where('status', 'pending')
            ->get()
            ->map(function($shipment) {
                $shipment->customer_name = $shipment->receiver->name ?? '-';
                return $shipment;
            });

        // In Transit
        $inTransit = Shipment::with('receiver')
            ->where('status', 'in_transit')
            ->get()
            ->map(function($shipment) {
                $shipment->customer_name = $shipment->receiver->name ?? '-';
                return $shipment;
            });

        // Delivered
        $delivered = Shipment::with('receiver')
            ->where('status', 'delivered')
            ->get()
            ->map(function($shipment) {
                $shipment->customer_name = $shipment->receiver->name ?? '-';
                return $shipment;
            });

        // Exception / Cancelled
        $exceptionShipments = Shipment::with('receiver')
            ->where('status', 'cancelled')
            ->get()
            ->map(function($shipment) {
                $shipment->customer_name = $shipment->receiver->name ?? '-';
                return $shipment;
            });

        $boxes = Box::all();
        $contacts = Contact::orderBy('name')->get();

        $stats = [
            'pending_orders' => $openOrders->count(),
            'shipped_orders' => Shipment::where('status', 'shipped')->count(),
            'in_transit' => $inTransit->count(),
            'delivered' => $delivered->count(),
            'exceptions' => $exceptionShipments->count(), 
        ];

        return view('shipping.dashboard', compact(
            'openOrders',
            'boxes',
            'contacts',
            'stats',
            'inTransit',
            'delivered',
            'exceptionShipments'
        ));
    }
}
