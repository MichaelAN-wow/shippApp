<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class UPSController extends Controller
{
    public function generateLabel(Request $request)
    {
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
            return response()->json(['error' => 'UPS token failed'], 500);
        }

        
        $shipmentPayload = [
            "ShipmentRequest" => [
                "Request" => [
                    "RequestOption" => "nonvalidate"
                ],
                "Shipment" => [
                    "Shipper" => [
                        "Name" => "Sender Inc",
                        "ShipperNumber" => config('services.ups.account_number'),
                        "Address" => [
                            "AddressLine" => ["1234 Origin St"],
                            "City" => "New York",
                            "StateProvinceCode" => "NY",
                            "PostalCode" => "10001",
                            "CountryCode" => "US"
                        ]
                    ],
                    "ShipTo" => [
                        "Name" => $request->name,
                        "Address" => [
                            "AddressLine" => [$request->address],
                            "City" => $request->city,
                            "StateProvinceCode" => $request->state,
                            "PostalCode" => $request->zip,
                            "CountryCode" => "US"
                        ]
                    ],
                    "Service" => [
                        "Code" => "03"
                    ],
                    "Package" => [
                        "Packaging" => [
                            "Code" => "02",
                            "Description" => "Package"
                        ],
                        "PackageWeight" => [
                            "UnitOfMeasurement" => ["Code" => "LBS"],
                            "Weight" => $request->weight
                        ]
                    ],
                    "LabelSpecification" => [
                        "LabelImageFormat" => ["Code" => "GIF"]
                    ]
                ]
            ]
        ];

        $labelResponse = $client->post('https://wwwcie.ups.com/api/shipments/v1/ship', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ],
            'json' => $shipmentPayload
        ]);

        $labelBody = json_decode($labelResponse->getBody(), true);

        $graphicImage = $labelBody['ShipmentResponse']['ShipmentResults']['PackageResults']['ShippingLabel']['GraphicImage'] ?? null;

        if (!$graphicImage) {
            return response()->json(['error' => 'UPS Generate Failed', 'response' => $labelBody], 500);
        }

        
        $path = storage_path('app/public/ups_label.gif');
        file_put_contents($path, base64_decode($graphicImage));

        return response()->json([
            'downloadUrl' => url('storage/ups_label.gif')
        ]);
    }
}
