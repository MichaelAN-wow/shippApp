<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Shopify\Clients\Rest;
use Shopify\Clients\Graphql;
use App\Models\Product;
use App\Models\ProductInventory;
use App\Models\ProductTransaction;
use App\Models\Category;
use App\Models\Sale;
use App\Models\ShopifyLocation;
use App\Models\Company;

use GuzzleHttp\Client;

class ShopifyController extends Controller
{

    protected $client;

    public function __construct()
    {
        $this->setShopifyClient();
    }

    private function setShopifyClient() {
        $user = auth()->user();
          // Check if user or company exists
        if ($user && $user->company && $user->company->shopify_domain && $user->company->shopify_token) {
            // Initialize the Shopify client with domain and token
            $this->client = new Graphql(
                $user->company->shopify_domain,
                $user->company->shopify_token
            );
        } else {
            // Handle the case where company or Shopify credentials do not exist
            $this->client = null; // Or set default or log an error
        }
    }

    protected function getCategoryId($categoryName)
    {
        if (empty($categoryName) || $categoryName == "") {
            return null;
        }
        //dd($categoryName);
        // Find or create the category
        $category = Category::firstOrCreate(
            ['name' => $categoryName],
            ['type' => 2] // 2 means products
        );

        return $category->id;
    }

    protected function downloadPhoto($photoUrl)
    {
        if ($photoUrl) {
            // Ensure the directory exists
            $directory = storage_path('app/public/products_photos');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Download and save the photo
            $contents = file_get_contents($photoUrl);
            $name = basename(parse_url($photoUrl, PHP_URL_PATH));
            $path = $directory . '/' . $name;
            file_put_contents($path, $contents);

            return 'products_photos/' . $name;
        }

        return null;
    }

    public function getNewOrders($latestSaleDate)
    {
        $orders = [];
        $hasNextPage = true;
        $cursor = null;

        while ($hasNextPage) {
            $response = $this->fetchOrders($cursor, $latestSaleDate);
            if (isset($response['data']['orders'])) {
                $data = $response['data']['orders'];
                $orders = array_merge($orders, $data['edges']);
                $hasNextPage = $data['pageInfo']['hasNextPage'];
                $cursor = end($data['edges'])['cursor'];
            } else {
                Log::error('Error fetching orders from Shopify: ', $response);
                break;
            }
        }

        return $orders;
    }

    private function fetchOrders($cursor, $latestSaleDate)
    {
        $cursorClause = $cursor ? ", after: \"$cursor\"" : '';
        $query = <<<GRAPHQL
        {
            orders(first: 200$cursorClause, query: "created_at:>=$latestSaleDate") {
                pageInfo {
                    hasNextPage
                }
                edges {
                    cursor
                    node {
                        id
                        name
                        createdAt
                        updatedAt
                        customer {
                            id
                        }
                        displayFinancialStatus
                        displayFulfillmentStatus
                        totalPriceSet {
                            shopMoney {
                                amount
                            }
                        }
                        totalDiscountsSet {
                            shopMoney {
                                amount
                            }
                        }
                        totalTaxSet {
                            shopMoney {
                                amount
                            }
                        }
                        lineItems(first: 50) {
                            edges {
                                node {
                                    id
                                    name
                                    quantity
                                    product {
                                        id
                                    }
                                    variant {
                                        id
                                    }
                                    originalUnitPriceSet {
                                        shopMoney {
                                            amount
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        GRAPHQL;

        $response = $this->client->query(['query' => $query]);
        return $response->getDecodedBody();
    }


    public function getAllOrders()
    {
        $orders = [];
        $hasNextPage = true;
        $cursor = null;

        while ($hasNextPage) {
            $response = $this->fetchAllOrders($cursor);
            // if (isset($response['errors'])) {
            //     Log::error('Shopify API Error: ', $response['errors']);
            //     break;
            // }
            if (isset($response['data']['orders'])) {
                $data = $response['data']['orders'];
                $orders = array_merge($orders, $data['edges']);
                $hasNextPage = $data['pageInfo']['hasNextPage'];
                $cursor = end($data['edges'])['cursor'];
            } else {
                //dd($response);
                break;
            }
        }

        return $orders;
    }

    private function fetchAllOrders($cursor)
    {
        $cursorClause = $cursor ? ", after: \"$cursor\"" : '';
        $query = <<<GRAPHQL
        {
            orders(first: 200$cursorClause, reverse: true) {
                pageInfo {
                    hasNextPage
                }
                edges {
                    cursor
                    node {
                        id
                        name
                        createdAt
                        updatedAt
                        customer {
                            id
                        }
                        displayFinancialStatus
                        displayFulfillmentStatus
                        totalPriceSet {
                            shopMoney {
                                amount
                            }
                        }
                        totalDiscountsSet {
                            shopMoney {
                                amount
                            }
                        }
                        totalTaxSet {
                            shopMoney {
                                amount
                            }
                        }
                        lineItems(first: 50) {
                            edges {
                                node {
                                    id
                                    name
                                    quantity
                                    product {
                                        id
                                    }
                                    variant {
                                        id
                                    }
                                    originalUnitPriceSet {
                                        shopMoney {
                                            amount
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        GRAPHQL;

        $response = $this->client->query(['query' => $query]);
        return $response->getDecodedBody();
    }


    public function getOrders()
    {

        $query = <<<GRAPHQL
        {
            orders(first: 50) {
                edges {
                    node {
                        id
                        name
                        createdAt
                        updatedAt
                        customer {
                            id
                        }
                        financialStatus
                        fulfillmentStatus
                        totalPriceSet {
                            shopMoney {
                                amount
                            }
                        }
                        lineItems(first: 50) {
                            edges {
                                node {
                                    id
                                    name
                                    quantity
                                    originalUnitPriceSet {
                                        shopMoney {
                                            amount
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        GRAPHQL;

        $this->setShopifyClient();
        $response = $this->client->query(['query' => $query]);

        $responseBody = $response->getDecodedBody();
        //dd($responseBody);
        // if (isset($responseBody['errors'])) {
        //     Log::error('Shopify API Error: ', $responseBody['errors']);
        //     return [];
        // }

        return $responseBody['data']['orders']['edges'];
    }

    public function getShopifyProducts(Request $request)
    {
        $cursor = $request->query('cursor', null);
        $response = $this->fetchProducts($cursor);

        if (isset($response['data']['products'])) {
            $data = $response['data']['products'];
            $products = $data['edges'];
            $hasNextPage = $data['pageInfo']['hasNextPage'];
            $nextCursor = $hasNextPage ? end($products)['cursor'] : null;

            $formattedProducts = [];
            foreach ($products as $productEdge) {
                $productNode = $productEdge['node'];
                foreach ($productNode['variants']['edges'] as $variantEdge) {
                    $variant = $variantEdge['node'];
                    $formattedName = $productNode['title'] . ' - ' . ($variant['title'] === 'Default Title' ? $productNode['title'] : $variant['title']);
                    $formattedProducts[] = [
                        'id' => $variant['id'],
                        'variants_id' => $productNode['id'],
                        'name' => $formattedName,
                        'stock_level' => $variant['inventoryQuantity'],
                    ];
                }
            }

            return response()->json([
                'products' => $formattedProducts,
                'next_cursor' => $nextCursor,
            ]);
        } else {
            return response()->json([
                'products' => [],
                'next_cursor' => null,
            ]);
        }
    }

    private function fetchProducts($cursor)
    {
        $cursorClause = $cursor ? ", after: \"$cursor\"" : '';
        $query = <<<GRAPHQL
        {
            products(first: 50$cursorClause, sortKey: TITLE) {
                pageInfo {
                    hasNextPage
                }
                edges {
                    cursor
                    node {
                        id
                        title
                        variants(first: 100) {
                            edges {
                                node {
                                    id
                                    title
                                    inventoryQuantity
                                }
                            }
                        }
                    }
                }
            }
        }
        GRAPHQL;
        
        $this->setShopifyClient();
        $response = $this->client->query(['query' => $query]);
        return $response->getDecodedBody();
    }

    public function getLocalProducts()
    {
        $products = Product::where('company_id', session('company_id'))->orderBy('name')->get();
        return response()->json($products);
    }

    public function saveProductMatch(Request $request)
    {
        $shopifyProductId = $request->input('shopify_product_id');
        $shopifyVariantsId = $request->input('shopify_variants_id');
        $localProductId = $request->input('local_product_id');

        $product = Product::find($localProductId);
        $product->shopify_id = $shopifyProductId;
        $product->variants_id = $shopifyVariantsId;
        $product->save();

        return response()->json(['message' => 'Product matched successfully.']);
    }

    public function syncProducts(Request $request)
    {
        $offset = $request->input('offset', 0);
        $product = Product::where('company_id', session('company_id'))->whereNotNull('shopify_id')->orderBy('name')->skip($offset)->first();

        if ($product) {
            $beforeSyncLevel = $product->current_stock_level;
            try {
                $shopifyProducts = $this->getShopifyProduct($product->variants_id, auth()->user()->company->shopify_domain, auth()->user()->company->shopify_token);
                if ($shopifyProducts) {
                    foreach ($shopifyProducts['variants'] as $shopifyProduct) {
                        if ($shopifyProduct['id'] == $product->shopify_id) {
                            $currentStockLevel = $shopifyProduct["inventory_quantity"];
                            $product->current_stock_level = $currentStockLevel;
                            $product->touch();
                            $product->save();
    
                            foreach ($shopifyProduct['inventory_levels'] as $inventoryLevel) {
                                $locationId = $inventoryLevel['location_id'];
                                $availableStockLevel = $inventoryLevel['available'];
        
                                // Find or create the product inventory record for the specific location
                                $productInventory = ProductInventory::firstOrCreate(
                                    ['product_id' => $product->id, 'location_id' => $locationId],
                                    ['min_stock_level' => 0] // Default min_stock_level if not set
                                );
        
                                // Update the current stock level in the inventory
                                $productInventory->current_stock_level = $availableStockLevel;   
                                $productInventory->touch(); 
                                $productInventory->save();
                            }
                            return response()->json([
                                'message' => $product->name . ': ' . $beforeSyncLevel . ' -> ' . $currentStockLevel . ' synced successfully.',
                                'afterSyncStockLevel' => $currentStockLevel
                            ]);
                        }
                    }
                    $product->touch();
                    return response()->json(['message' => 'Shopify ID is different.'], 200);
                } else {
                    $product->current_stock_level = 0;
                    $product->touch();
                    $product->save();
                    return response()->json(['message' => 'Shopify product not found.'], 200);
                }
            } catch (ClientException $e) {
                $product->touch();
                $product->save();
                return response()->json(['message' => 'Shopify product not found.'], 200);
            }
            
        }
        return response()->json(['message' => 'No more products to sync.'], 200);
    }

    public function getShopifyProduct($productId, $shopify_domain, $shopify_token)
    {
        $shopify = new Client([
            'base_uri' => 'https://' . $shopify_domain . '/admin/api/2024-04/',
            'headers' => [
                'X-Shopify-Access-Token' => $shopify_token,
                'Content-Type' => 'application/json',
            ],
        ]);

        try {
            // Make the GET request to fetch the product details
            $response = $this->makeShopifyRequest($shopify, 'products/'.$productId.'.json');
            
            // Decode the response body to get product data
            $products = json_decode($response->getBody()->getContents(), true);

            // Check if the 'product' key is in the response
            if (isset($products['product'])) {
                $inventoryItemIds = array_column($products['product']['variants'], 'inventory_item_id');
                sleep(1);
                // Fetch inventory levels in bulk
                $inventoryResponse = $this->makeShopifyRequest($shopify, 'inventory_levels.json', [
                    'query' => [
                        'inventory_item_ids' => implode(',', $inventoryItemIds),
                    ]
                ]);

                $inventoryLevels = json_decode($inventoryResponse->getBody()->getContents(), true);

                // Map inventory levels to inventory_item_ids
                $inventoryMap = [];
                foreach ($inventoryLevels['inventory_levels'] as $level) {
                    $inventoryMap[$level['inventory_item_id']][] = $level;
                }

                // Attach inventory levels to the corresponding variants
                foreach ($products['product']['variants'] as &$variant) {
                    $variant['inventory_levels'] = $inventoryMap[$variant['inventory_item_id']] ?? [];
                }
            }

            // Return the product data
            return $products['product'] ?? null;

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Handle request errors (e.g., network issues, 4xx or 5xx errors)
            Log::error("Shopify API request failed for product ID {$productId}: " . $e->getMessage());
            return null; // You can also return a custom error response or throw an exception if needed
        } catch (\Exception $e) {
            // Handle other general errors (e.g., decoding errors, unexpected issues)
            Log::error("An error occurred while fetching product ID {$productId} from Shopify: " . $e->getMessage());
            return null;
        }
    }


    private function makeShopifyRequest($shopify, $endpoint, $params = [], $retryCount = 0)
    {
        try {
            // Perform the request without delay initially
            return $shopify->get($endpoint, $params);
        } catch (ClientException $e) {
            if ($e->getCode() == 429) {
                // If rate limit exceeded, wait for 1 second and retry
                $retryAfter = $e->getResponse()->getHeader('Retry-After')[0] ?? 2; // Default 2 seconds if header not available
                sleep((int) $retryAfter + 1);

                // Optionally, you could add retry count logic to avoid infinite retries.
                if ($retryCount < 5) {
                    return $this->makeShopifyRequest($shopify, $endpoint, $params, ++$retryCount); // Retry the request with incremented retry count
                } else {
                    throw new \Exception('Max retry attempts exceeded for rate limiting');
                }
            }

            // Re-throw the exception if it's not a rate limit issue
            throw $e;
        }
    }

    private function getShopifyLocations()
    {
        $shopify = new Client([
            'base_uri' => 'https://' . auth()->user()->company->shopify_domain . '/admin/api/2024-04/',
            'headers' => [
                'X-Shopify-Access-Token' => auth()->user()->company->shopify_token,
                'Content-Type' => 'application/json',
            ],
        ]);

        $response = $shopify->get('locations.json');
        $locations = json_decode($response->getBody()->getContents(), true);

        return $locations['locations'] ?? [];
    }

    private function getLocalLocations()
    {
        return ShopifyLocation::all()->pluck('name', 'id')->toArray();
    }

    public function updateShopifyLocationTable()
    {
        $shopifyLocations = $this->getShopifyLocations();
        $localLocations = $this->getLocalLocations();

        // Check if there are any differences
        $locationsToUpdate = [];
        foreach ($shopifyLocations as $location) {
            if (!isset($localLocations[$location['id']]) || $localLocations[$location['id']] !== $location['name']) {
                $locationsToUpdate[] = [
                    'id' => $location['id'],
                    'name' => $location['name']
                ];
            }
        }

        // If there are differences, truncate the table and insert new records
        if (!empty($locationsToUpdate)) {
            DB::transaction(function () use ($locationsToUpdate) {
                ShopifyLocation::truncate();
                ShopifyLocation::insert($locationsToUpdate);
            });
        }
    }

    private function getShopifyLocationIdByName($locationName)
    {
        $locations = $this->getShopifyLocations();

        if (!empty($locations)) {
            foreach ($locations as $location) {
                if ($location['name'] == $locationName) {
                    return $location['id'];
                }
            }
        }

        return null;
    }

    private function getShopifyProductInventoryItemId($shopifyId, $variantId)
    {
        $shopify = new Client([
            'base_uri' => 'https://' . auth()->user()->company->shopify_domain . '/admin/api/2024-04/',
            'headers' => [
                'X-Shopify-Access-Token' => auth()->user()->company->shopify_token,
                'Content-Type' => 'application/json',
            ],
        ]);

        $response = $shopify->get("products/{$shopifyId}/variants.json");
        $variants = json_decode($response->getBody()->getContents(), true);

        foreach ($variants['variants'] as $variant) {
            if ($variant['id'] == $variantId) {
                return $variant['inventory_item_id'];
            }
        }

        return null;
    }


    public function updateShopifyStockLevel($product, $quantity, $locationName, &$locationCache = [])
    {
        // Use cache if available
        if (isset($locationCache[$locationName])) {
            $locationId = $locationCache[$locationName];
        } else {
            $locationId = $this->getShopifyLocationIdByName($locationName);
            $locationCache[$locationName] = $locationId;
            usleep(600000);
        }

        $inventoryItemId = $this->getShopifyProductInventoryItemId($product->variants_id, $product->shopify_id);
        usleep(600000);

        if ($inventoryItemId) {
            $shopify = new Client([
                'base_uri' => 'https://' . auth()->user()->company->shopify_domain . '/admin/api/2024-04/',
                'headers' => [
                    'X-Shopify-Access-Token' => auth()->user()->company->shopify_token,
                    'Content-Type' => 'application/json',
                ],
            ]);

            $response = $shopify->post('inventory_levels/adjust.json', [
                'json' => [
                    'inventory_item_id' => $inventoryItemId,
                    'location_id' => $locationId,
                    'available_adjustment' => $quantity,
                ],
            ]);

            if (json_decode($response->getBody()->getContents(), true) !== null)
                return true;
            return false;
        }

        return false;
    }

    public function saveShopifyData(Request $request)
    {
        // Validate incoming data
        $request->validate([
            'shopify_domain' => 'required|string',
            'access_token' => 'required|string',
        ]);

        // Get the company of the authenticated user
        $company = auth()->user()->company;

        // Check if company exists
        if (!$company) {
            return response()->json(['success' => false, 'msg' => 'Company not found.'], 404);
        }

        // Save the Shopify domain and token to the company's record
        $company->shopify_domain = $request->input('shopify_domain');
        $company->shopify_token = $request->input('access_token');
        $company->save();

        // Return success response
        return response()->json(['success' => true, 'msg' => 'Shopify domain and access token saved successfully.']);
    }

    public function validateShopifyCredentials($shopify_domain, $shopify_token)
    {
        try {
            $this->client = new Graphql($shopify_domain, $shopify_token);
            // Example test query to check if token and domain are valid
            $query = <<<GRAPHQL
            {
                shop {
                    name
                }
            }
            GRAPHQL;
            
            $response = $this->client->query(['query' => $query]);
            return isset($response->getDecodedBody()['data']['shop']);
        } catch (\Exception $e) {
            return false;
        }
    }
}
