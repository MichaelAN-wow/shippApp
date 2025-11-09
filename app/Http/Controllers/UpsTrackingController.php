<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Services\Carriers\UpsTrackingService;

class TrackingController extends Controller
{
    protected $upsTracking;

    public function __construct(UpsTrackingService $upsTracking)
    {
        $this->upsTracking = $upsTracking;
    }

    public function autoUpdate($id)
    {
        $shipment = Shipment::findOrFail($id);

        
        $status = $this->upsTracking->getStatus($shipment->tracking_number);

        $shipment->status = $status;
        $shipment->save();

        return redirect()->back()->with('success', "Label {$shipment->tracking_number} Status '{$status}'updated");
    }
}
