<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Services\Carriers\UpsTrackingService;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    protected UpsTrackingService $ups;

    public function __construct(UpsTrackingService $ups)
    {
        $this->ups = $ups;
    }

    /**
     * Auto update one UPS shipment and return JSON for AJAX.
     * GET /shipments/{id}/auto-update
     */
    public function autoUpdate($id)
    {
        $shipment = Shipment::findOrFail($id);

        if (strtoupper($shipment->carrier) !== 'UPS' || empty($shipment->tracking_number)) {
            return response()->json([
                'success' => false,
                'message' => 'Not an UPS shipment or missing tracking number.',
            ], 400);
        }

        try {
            $data = $this->ups->getStatus($shipment->tracking_number);

            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tracking data from UPS.',
                ], 502);
            }

            // Persist to DB
            $shipment->status             = $data['status'] ?? $shipment->status;
            $shipment->status_code        = $data['status_code'] ?? $shipment->status_code;
            $shipment->status_description = $data['status_description'] ?? $shipment->status_description;
            $shipment->estimated_delivery = $data['estimated_delivery'] ?? $shipment->estimated_delivery;
            $shipment->last_location      = $data['last_location'] ?? $shipment->last_location;
            $shipment->delivered_at       = $data['delivered_at'] ?? $shipment->delivered_at;
            $shipment->last_tracked_at    = now();
            if (!empty($data['raw'])) {
                $shipment->raw_tracking = json_encode($data['raw']);
            }
            $shipment->save();

            return response()->json([
                'success'            => true,
                'status'             => $shipment->status,
                'status_description' => $shipment->status_description,
                'estimated_delivery' => $shipment->estimated_delivery,
                'last_location'      => $shipment->last_location,
                'delivered_at'       => $shipment->delivered_at,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'UPS update failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Auto update all UPS shipments in bulk (optional if you already have another flow).
     * GET /shipments/auto-update-all
     */
    public function autoUpdateAll()
    {
        $shipments = Shipment::query()
            ->where('carrier', 'UPS')
            ->whereNotNull('tracking_number')
            ->whereIn('status', ['pending', 'in_transit'])
            ->get();

        $count = 0;
        foreach ($shipments as $s) {
            try {
                $data = $this->ups->getStatus($s->tracking_number);
                if ($data) {
                    $s->status             = $data['status'] ?? $s->status;
                    $s->status_code        = $data['status_code'] ?? $s->status_code;
                    $s->status_description = $data['status_description'] ?? $s->status_description;
                    $s->estimated_delivery = $data['estimated_delivery'] ?? $s->estimated_delivery;
                    $s->last_location      = $data['last_location'] ?? $s->last_location;
                    $s->delivered_at       = $data['delivered_at'] ?? $s->delivered_at;
                    $s->last_tracked_at    = now();
                    if (!empty($data['raw'])) {
                        $s->raw_tracking = json_encode($data['raw']);
                    }
                    $s->save();
                    $count++;
                }
            } catch (\Throwable $e) {
                // swallow single item error
            }
        }

        return back()->with('success', "UPS auto update completed for {$count} shipments.");
    }
}
