<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use App\Models\Contact;
use App\Models\Box;
use App\Models\Shipment;

class ShippingManagerController extends Controller
{
    
    public function index(Request $request)
    {
        $query = DB::table('shipments')
            ->leftJoin('contacts as senders', 'shipments.sender_id', '=', 'senders.id')
            ->leftJoin('contacts as receivers', 'shipments.receiver_id', '=', 'receivers.id')
            ->select(
                'shipments.*',
                'shipments.tracking_number as tracking_no',
                'senders.name as sender_name',
                'receivers.name as receiver_name'
            )
            ->orderByDesc('shipments.created_at');

        if ($request->filled('search')) {
            $query->where('shipments.tracking_number', 'like', '%'.$request->search.'%');
        }
        if ($request->filled('carrier')) {
            $query->where('shipments.carrier', $request->carrier);
        }
        if ($request->filled('status')) {
            $query->where('shipments.status', $request->status);
        }

        $shipments = $query->paginate(10);
        return view('shipping.shipped_packages', compact('shipments'));
    }

    
    public function create($orderId = null)
    {
        $contacts = Contact::orderBy('name')->get();
        $boxes = Box::orderBy('name')->get();
        return view('shipping.create_label', compact('contacts', 'boxes', 'orderId'));
    }

    
    public function store(Request $request, $orderId = null)
    {
        $request->validate([
            'sender_id'   => 'required|exists:contacts,id',
            'receiver_id' => 'required|exists:contacts,id',
            'box_id'      => 'required|exists:boxes,id',
            'product_weight_lb' => 'required|integer|min:0',
            'product_weight_oz' => 'required|numeric|min:0|max:15.99',
        ]);
        try {
            $sender   = Contact::findOrFail($request->sender_id);
            $receiver = Contact::findOrFail($request->receiver_id);
            $box      = Box::findOrFail($request->box_id);

            $boxWeightLb = intval($box->weight_lb ?? 0);
            $boxWeightOz = floatval($box->weight_oz ?? 0);

            $prodLb = intval($request->product_weight_lb);
            $prodOz = floatval($request->product_weight_oz);

            $totalWeightLbs = ($boxWeightLb + $prodLb) + ($boxWeightOz + $prodOz)/16;
            $totalWeightLbs = max(0.1, $totalWeightLbs);

            $countryMap = [
                'USA' => 'US', 'United States' => 'US',
                'Mexico' => 'MX', 'Germany' => 'DE', 'Japan' => 'JP',
            ];
            $senderCountry   = $countryMap[$sender->country] ?? strtoupper(substr($sender->country,0,2));
            $receiverCountry = $countryMap[$receiver->country] ?? strtoupper(substr($receiver->country,0,2));

            $client = new Client();
            $auth   = base64_encode(config('services.ups.client_id') . ':' . config('services.ups.client_secret'));
            $authResponse = $client->post('https://wwwcie.ups.com/security/v1/oauth/token', [
                'headers' => [
                    'Authorization' => "Basic $auth",
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
                'form_params' => ['grant_type'=>'client_credentials']
            ]);

            $accessToken = json_decode($authResponse->getBody(), true)['access_token'] ?? null;
            if (!$accessToken) throw new \Exception("UPS Access Token not received");

            $maxLengthInches = 108;
            $lengthInches = $box->length * 0.393701;
            if ($lengthInches > $maxLengthInches) throw new \Exception("Box length exceeds UPS max allowed ($maxLengthInches inches)");

            // UPS Shipment Payload
            $payload = [
                "ShipmentRequest" => [
                    "Request" => ["RequestOption" => "nonvalidate"],
                    "Shipment" => [
                        "Shipper" => [
                            "Name" => $sender->name,
                            "ShipperNumber" => config('services.ups.account_number'),
                            "Address" => [
                                "AddressLine"=>[$sender->street],
                                "City"=>$sender->city,
                                "StateProvinceCode"=>$sender->state,
                                "PostalCode"=>$sender->zip,
                                "CountryCode"=>$senderCountry
                            ]
                        ],
                        "ShipTo" => [
                            "Name"=>$receiver->name,
                            "Address"=>[
                                "AddressLine"=>[$receiver->street],
                                "City"=>$receiver->city,
                                "StateProvinceCode"=>$receiver->state,
                                "PostalCode"=>$receiver->zip,
                                "CountryCode"=>$receiverCountry
                            ]
                        ],
                        "PaymentInformation"=>[
                            "ShipmentCharge"=>["Type"=>"01", "BillShipper"=>["AccountNumber"=>config('services.ups.account_number')]]
                        ],
                        "Service"=>["Code"=>"03"],
                        "Package"=>[
                            "Packaging"=>["Code"=>"02"],
                            "Dimensions"=>[
                                "UnitOfMeasurement"=>["Code"=>"IN"],
                                "Length" => (string) max(1,$box->length),
                                "Width"  => (string) max(1,$box->width),
                                "Height" => (string) max(1,$box->height)
                            ],
                            "PackageWeight"=>[
                                "UnitOfMeasurement"=>["Code"=>"LBS"],
                                "Weight" => (string)$totalWeightLbs
                            ]
                        ],
                        "LabelSpecification"=>["LabelImageFormat"=>["Code"=>"GIF"]]
                    ]
                ]
            ];

            $res = $client->post('https://wwwcie.ups.com/api/shipments/v1/ship', [
                'headers' => ['Authorization'=>"Bearer $accessToken",'Content-Type'=>'application/json'],
                'json' => $payload
            ]);

            $resBody = json_decode($res->getBody(), true);
            $trackingNumber = $resBody['ShipmentResponse']['ShipmentResults']['PackageResults']['TrackingNumber'] ?? null;
            $graphicImage   = $resBody['ShipmentResponse']['ShipmentResults']['PackageResults']['ShippingLabel']['GraphicImage'] ?? null;

            if (!$trackingNumber || !$graphicImage) throw new \Exception("Label not generated");

            $fileName = 'ups_label_'.time().'.gif';
            Storage::disk('public')->put($fileName, base64_decode($graphicImage));

            
            $shipment = Shipment::firstOrNew([
                'order_id' => $orderId,
                'sender_id'=> $sender->id,
                'receiver_id'=> $receiver->id,
                'box_id'=> $box->id
            ]);

            $shipment->product_weight_lb = $prodLb;
            $shipment->product_weight_oz = $prodOz;
            $shipment->box_weight_lb = $boxWeightLb;
            $shipment->box_weight_oz = $boxWeightOz;
            $shipment->total_weight_lbs = $totalWeightLbs;
            $shipment->carrier = 'UPS';
            $shipment->tracking_number = $trackingNumber;
            $shipment->label_path = $fileName;
            $shipment->status = 'in_transit';
            $shipment->save();

            if ($box->quantity > 0) $box->decrement('quantity', 1);
            return redirect()->route('shipping.dashboard')
                ->with('success','UPS label purchased successfully!')
                ->with('label_gif', asset('storage/'.$fileName));

        } catch (\Exception $e) {
            dd($e);
            return back()->withErrors(['UPS Exception'=>$e->getMessage()]);
        }
    }

    
    public function downloadLabel($shipmentId)
    {
        $shipment = Shipment::find($shipmentId);
        if(!$shipment || !Storage::disk('public')->exists($shipment->label_path)){
            return redirect()->back()->with('error','Label not found');
        }
        return response()->download(storage_path('app/public/'.$shipment->label_path));
    }

    
    public function updateStatus(Request $request, $shipmentId)
    {
        $request->validate([
            'status' => 'required|string|in:pending,in_transit,delivered,cancelled'
        ]);

        $shipment = Shipment::findOrFail($shipmentId);
        $shipment->status = $request->status;
        $shipment->save();

        return response()->json(['success' => true]);
    }

    
    public function trackPastPackage(Request $request)
    {
        $request->validate([
            'tracking_number' => 'required|string'
        ]);

        try {
            $trackingNumber = $request->tracking_number;
            $client = new Client();
            $auth   = base64_encode(config('services.ups.client_id') . ':' . config('services.ups.client_secret'));
            $authResponse = $client->post('https://wwwcie.ups.com/security/v1/oauth/token', [
                'headers'=>['Authorization'=>"Basic $auth",'Content-Type'=>'application/x-www-form-urlencoded'],
                'form_params'=>['grant_type'=>'client_credentials']
            ]);
            $accessToken = json_decode($authResponse->getBody(), true)['access_token'] ?? null;
            if(!$accessToken) throw new \Exception("UPS Access Token not received");

            $res = $client->get("https://wwwcie.ups.com/track/v1/details/$trackingNumber", [
                'headers'=>['Authorization'=>"Bearer $accessToken",'Accept'=>'application/json']
            ]);

            $resBody = json_decode($res->getBody(), true);
            $status = $resBody['trackResponse']['shipment'][0]['package'][0]['activity'][0]['status']['description'] ?? 'Unknown';
            return back()->with('success',"Tracking status: $status");

        } catch (\Exception $e) {
            return back()->withErrors(['UPS Tracking Exception'=>$e->getMessage()]);
        }
    }
}
