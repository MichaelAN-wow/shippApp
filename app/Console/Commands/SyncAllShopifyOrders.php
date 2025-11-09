<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ShopifyController;

use App\Models\Sale;
use App\Models\SaleProduct;
use App\Models\Company;

class SyncAllShopifyOrders extends Command
{
    protected $signature = 'shopify:sync-orders-all';
    protected $description = 'Sync all orders from Shopify to local database';


    protected $shopifyController;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ShopifyController $shopifyController)
    {
        parent::__construct();
        $this->shopifyController = $shopifyController;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $companies = Company::whereNotNull('shopify_token')
        ->whereNotNull('shopify_domain')
        ->get();

        foreach ($companies as $company) {
            $this->info($company->name);
            $this->info($company->shopify_token);
            if (!$this->shopifyController->validateShopifyCredentials($company->shopify_domain, $company->shopify_token)) {
                $this->warn("Invalid Shopify credentials for company: {$company->name}");
                continue; // Skip to the next company
            }

            $orders = $this->shopifyController->getAllOrders();

            foreach ($orders as $orderData) {
                
                $order = $orderData['node'];
                $this->info($order['id']);

                preg_match('/\d+/', $order['id'], $matches);
                $shopifyOrderId = $matches[0];

                $saleDate = (new \DateTime($order['createdAt']))->format('Y-m-d H:i:s');

                $sale = Sale::updateOrCreate(
                    ['shopify_id' => $shopifyOrderId],
                    [
                        'sale_type' => 'Shopify',
                        'shopify_order_name' => $order['name'],
                        'sale_date' => $saleDate,
                        'customer_id' => null,
                        'total' => $order['totalPriceSet']['shopMoney']['amount'],
                        'discount' =>$order['totalDiscountsSet']['shopMoney']['amount'],
                        'tax' => $order['totalTaxSet']['shopMoney']['amount'],
                        'status' => $order['displayFinancialStatus'],
                        'fulfillment_status' => $order['displayFulfillmentStatus'],
                        'shipping' => 'Shipped',
                        'company_id' => $company->id
                    ]
                );

                foreach ($order['lineItems']['edges'] as $lineItemData) {
                    $lineItem = $lineItemData['node'];
                    
                    $shopify_id = isset($lineItem['id']) ? $lineItem['id'] : null;
                    if ($shopify_id) {
                        preg_match('/\d+/', $shopify_id, $idMatches);
                        $shopify_id = $idMatches[0] ?? null;
                    }

                    $variantId = isset($lineItem['variant']['id']) ? $lineItem['variant']['id'] : null;
                    if ($variantId) {
                        preg_match('/\d+/', $variantId, $variantMatches);
                        $variantId = $variantMatches[0] ?? null;
                    }

                    // Extract product ID from the line item product ID, if present
                    $shopifyProductId = isset($lineItem['product']['id']) ? $lineItem['product']['id'] : null;
                    if ($shopifyProductId) {
                        preg_match('/\d+/', $shopifyProductId, $productMatches);
                        $shopifyProductId = $productMatches[0] ?? null;
                    }
                    
                    SaleProduct::updateOrCreate(
                        ['shopify_id' => $shopify_id],
                        [
                            'sale_id' => $sale->id,
                            'shopify_variants_id' => $variantId,
                            'shopify_product_id' => $shopifyProductId,
                            'product_name' => $lineItem['name'],
                            'quantity' => $lineItem['quantity'],
                            'unit_price' => $lineItem['originalUnitPriceSet']['shopMoney']['amount'],
                            'company_id' => $company->id
                        ]
                    );
                    
                }
            }

        }
        $this->info('Shopify orders have been synced successfully.');
    }
}
