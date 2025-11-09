<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class UPSOAuthController extends Controller
{
    public function generateLabel(Request $request)
    {
        $client = new Client();

        // Step 1: Get OAuth2 token
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
            return response()->json(['error' => 'Failed to retrieve UPS OAuth token'], 500);
        }

        // Step 2: Create label
        $shipmentPayload = [
            "ShipmentRequest" => [
                "Request" => [
                    "RequestOption" => "nonvalidate"
                ],
                "Shipment" => [
                    "Shipper" => [
                        "Name" => "Doug Eleven // Three",
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
                        "Name" => "Receiver",
                        "Address" => [
                            "AddressLine" => ["456 Receiver Rd"],
                            "City" => "Los Angeles",
                            "StateProvinceCode" => "CA",
                            "PostalCode" => "90001",
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
                    "Service" => [
                        "Code" => "03" // UPS Ground
                    ],
                    "Package" => [
                        [
                            "Packaging" => [
                                "Code" => "02",
                                "Description" => "Nails"
                            ],
                            "Dimensions" => [
                                "UnitOfMeasurement" => ["Code" => "IN"],
                                "Length" => "10",
                                "Width" => "5",
                                "Height" => "4"
                            ],
                            "PackageWeight" => [
                                "UnitOfMeasurement" => ["Code" => "LBS"], 
                                "Weight" => sprintf("%.1f", (float) $request->input('weight', 1.0))
                            ]
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
        $trackingNumber = $labelBody['ShipmentResponse']['ShipmentResults']['PackageResults']['TrackingNumber'] ?? null;
        $graphicImage = $labelBody['ShipmentResponse']['ShipmentResults']['PackageResults']['ShippingLabel']['GraphicImage'] ?? null;

        if (!$graphicImage) {
            return response()->json(['error' => 'Label not generated', 'details' => $labelBody], 500);
        }

        $path = storage_path('app/public/ups_oauth_label.gif');
        file_put_contents($path, base64_decode($graphicImage));

        return response()->download($path, 'ups_oauth_label.gif');
    }
}
