<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ShopifyController;

use App\Models\Sale;
use App\Models\SaleProduct;

class SyncShopifyOrders extends Command
{
    protected $signature = 'shopify:sync-orders';
    protected $description = 'Sync orders from Shopify to local database';


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
        set_time_limit(3000);

        $latestSaleDate = Sale::orderBy('sale_date', 'desc')->value('sale_date');
        if (!$latestSaleDate) {
            $latestSaleDate = '2000-01-01T00:00:00Z'; // default to a very old date if no sales exist
        }


        $orders = $this->shopifyController->getNewOrders($latestSaleDate);

        foreach ($orders as $orderData) {
            $order = $orderData['node'];

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
                    'shipping' => 'Shipped'
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
                        'unit_price' => $lineItem['originalUnitPriceSet']['shopMoney']['amount']
                    ]
                );
                
            }
        }

        $this->info('Shopify orders have been synced successfully.');
    }
}
