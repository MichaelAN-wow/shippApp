<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

class ShippingLabelController extends Controller
{
    private function upsGetAccessToken()
    {
        $clientId = env('UPS_CLIENT_ID');
        $clientSecret = env('UPS_CLIENT_SECRET');

        $client = new \GuzzleHttp\Client();

        $response = $client->post(env('UPS_BASE_URL') . '/security/v1/oauth/token', [
            'headers' => [
                'Content-Type'  => 'application/x-www-form-urlencoded',
                'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret),
            ],
            'form_params' => [
                'grant_type' => 'client_credentials',
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        
        dd($data);
    }


    private function createUpsShipment(array $shipmentData)
    {
        
        $client = new \GuzzleHttp\Client();
        $tokenResponse = $client->post(env('UPS_BASE_URL') . '/security/v1/oauth/token', [
            'auth' => [
                env('UPS_CLIENT_ID'),
                env('UPS_CLIENT_SECRET'),
            ],
            'form_params' => [
                'grant_type' => 'client_credentials',
            ],
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ]);

        $tokenData = json_decode($tokenResponse->getBody(), true);
        if (!isset($tokenData['access_token'])) {
            throw new \Exception('UPS token retrieval failed: ' . json_encode($tokenData));
        }

        $accessToken = $tokenData['access_token'];

        
        $response = $client->post(env('UPS_BASE_URL') . '/ship/v1801/shipments', [
            'headers' => [
                'Authorization'   => 'Bearer ' . $accessToken,
                'Content-Type'    => 'application/json',
                'transId'         => uniqid(),
                'transactionSrc'  => 'MyApp',
            ],
            'json' => $shipmentData
        ]);

        return json_decode($response->getBody(), true);
    }


    public function create($orderId)
    {
        $order = Order::findOrFail($orderId);
        return view('shipping.create_label', compact('order'));
    }

    public function store(Request $request, $orderId)
    {
        if (!$orderId) {
            return back()->withErrors('Order ID is missing.');
        }

        $validated = $request->validate([
            'recipient_name' => 'required|string|max:255',
            'address_line1'  => 'required|string',
            'address_line2'  => 'nullable|string',
            'city'           => 'required|string',
            'state'          => 'required|string',
            'postal_code'    => 'required|string',
            'country'        => 'required|string|size:2',
            'package_weight' => 'required|numeric|min:0.1',
        ]);

        $shipmentData = [
            'ShipmentRequest' => [
                'Shipment' => [
                    'Shipper' => [
                        'Name' => 'My Company',
                        'ShipperNumber' => env('UPS_ACCOUNT_NUMBER'),
                        'Address' => [
                            'AddressLine'       => ['123 Main St'],
                            'City'              => 'New York',
                            'StateProvinceCode' => 'NY',
                            'PostalCode'        => '10001',
                            'CountryCode'       => 'US'
                        ]
                    ],
                    'ShipTo' => [
                        'Name' => $validated['recipient_name'],
                        'Address' => [
                            'AddressLine'       => [$validated['address_line1']],
                            'City'              => $validated['city'],
                            'StateProvinceCode' => $validated['state'],
                            'PostalCode'        => $validated['postal_code'],
                            'CountryCode'       => $validated['country']
                        ]
                    ],
                    'Package' => [
                        [
                            'Packaging' => ['Code' => '02'],
                            'PackageWeight' => [
                                'UnitOfMeasurement' => ['Code' => 'KGS'],
                                'Weight'            => (string)$validated['package_weight']
                            ]
                        ]
                    ],
                    'Service' => [
                        'Code'        => '03', // Ground
                        'Description' => 'UPS Ground'
                    ]
                ],
                'LabelSpecification' => [
                    'LabelImageFormat' => ['Code' => 'GIF']
                ]
            ]
        ];

        $upsResponse = $this->createUpsShipment($shipmentData);

        $trackingNumber = $upsResponse['ShipmentResponse']['ShipmentResults']['PackageResults']['TrackingNumber'] ?? null;

        DB::table('shipments')->insert([
            'order_reference' => $orderId,
            'tracking_number' => $trackingNumber,
            'carrier'         => 'UPS',
            'status'          => 'in_transit',
            'weight_kg'       => $validated['package_weight'],
            'destination'     => $validated['city'] . ', ' . $validated['state'] . ' ' . $validated['postal_code'],
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        return redirect()->route('shipping.index')->with('success', 'UPS Shipping label created. Tracking No: ' . $trackingNumber);
    }
    public function testUpsToken()
    {
        try {
            $client = new \GuzzleHttp\Client();

            $response = $client->post(env('UPS_BASE_URL') . '/security/v1/oauth/token', [
                'auth' => [
                    env('UPS_CLIENT_ID'),
                    env('UPS_CLIENT_SECRET'),
                ],
                'form_params' => [
                    'grant_type' => 'client_credentials',
                ],
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
