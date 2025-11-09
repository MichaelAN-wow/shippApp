<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use App\Models\Shipment;

class ShippingController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'nullable|integer',
            'recipient_name' => 'required|string|max:255',
            'street_address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:50',
            'postal_code' => 'required|string|max:20',
            'weight' => 'required|numeric',
        ]);

        try {
            $client = new Client();

            
            $auth = base64_encode(config('services.ups.client_id') . ':' . config('services.ups.client_secret'));

            $authResponse = $client->post('https://wwwcie.ups.com/security/v1/oauth/token', [
                'headers' => [
                    'Authorization' => 'Basic ' . $auth,
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
                'form_params' => [
                    'grant_type' => 'client_credentials'
                ]
            ]);

            $tokenData = json_decode($authResponse->getBody(), true);
            $accessToken = $tokenData['access_token'] ?? null;

            if (!$accessToken) {
                return back()->withErrors(['UPS Token Error' => 'Failed to retrieve UPS OAuth token']);
            }

            
            $shipmentPayload = [
                "ShipmentRequest" => [
                    "Request" => ["RequestOption" => "nonvalidate"],
                    "Shipment" => [
                        "Shipper" => [
                            "Name" => "Your Company",
                            "ShipperNumber" => config('services.ups.account_number'),
                            "Address" => [
                                "AddressLine" => ["123 Sender St"],
                                "City" => "New York",
                                "StateProvinceCode" => "NY",
                                "PostalCode" => "10001",
                                "CountryCode" => "US"
                            ],
                        ],
                        "ShipTo" => [
                            "Name" => $request->recipient_name,
                            "Address" => [
                                "AddressLine" => [$request->street_address],
                                "City" => $request->city,
                                "StateProvinceCode" => $request->state,
                                "PostalCode" => $request->postal_code,
                                "CountryCode" => "US"
                            ],
                        ],
                        "PaymentInformation" => [
                            "ShipmentCharge" => [
                                "Type" => "01",
                                "BillShipper" => [
                                    "AccountNumber" => config('services.ups.account_number'),
                                ],
                            ],
                        ],
                        "Service" => ["Code" => "03"],
                        "Package" => [
                            "Packaging" => ["Code" => "02", "Description" => "Package"],
                            "PackageWeight" => ["UnitOfMeasurement" => ["Code" => "KGS"], "Weight" => (string) $request->weight]
                        ],
                        "LabelSpecification" => ["LabelImageFormat" => ["Code" => "GIF"]]
                    ]
                ]
            ];

            // UPS Shipment API
            $labelResponse = $client->post('https://wwwcie.ups.com/api/shipments/v1/ship', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json'
                ],
                'json' => $shipmentPayload
            ]);

            $labelBody = json_decode($labelResponse->getBody(), true);

            $graphicImage = $labelBody['ShipmentResponse']['ShipmentResults']['PackageResults']['ShippingLabel']['GraphicImage'] ?? null;
            $trackingNumber = $labelBody['ShipmentResponse']['ShipmentResults']['ShipmentIdentificationNumber'] ?? null;

            if (!$graphicImage) {
                return back()->withErrors(['UPS Error' => 'Label not generated']);
            }


            $fileName = 'ups_labels/' . 'ups_label_' . time() . '.gif';
            Storage::disk('public')->put($fileName, base64_decode($graphicImage));


            $shipment = Shipment::create([
                'order_id' => $request->input('order_id'),
                'tracking_number' => $trackingNumber,
                'carrier' => 'UPS',
                'status' => 'shipped',
                'label_path' => $fileName,
            ]);

            return redirect()->route('shipping.shipped')->with('success', 'Label created successfully.');

        } catch (\Exception $e) {
            return back()->withErrors(['UPS Exception' => $e->getMessage()]);
        }
    }


    public function shippedPackages()
    {
        $shipments = Shipment::orderBy('created_at', 'desc')->paginate(20);
        return view('shipping.shipped_packages', compact('shipments'));
    }
}
