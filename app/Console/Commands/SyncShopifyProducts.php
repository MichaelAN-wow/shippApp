<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;


use App\Models\Product;
use App\Models\Company;
use App\Models\ProductInventory;

use App\Http\Controllers\ShopifyController;
use Illuminate\Support\Facades\Log;

class SyncShopifyProducts extends Command
{
    protected $signature = 'shopify:sync-products';
    protected $description = 'Sync products from Shopify to local database';

    protected $shopifyController;

    public function __construct(ShopifyController $shopifyController)
    {
        parent::__construct();
        $this->shopifyController = $shopifyController;
    }
    public function handle()
    {
        $companies = Company::whereNotNull('shopify_token')
        ->whereNotNull('shopify_domain')
        ->get();

        foreach ($companies as $company) {
            if (!$this->shopifyController->validateShopifyCredentials($company->shopify_domain, $company->shopify_token)) {
                $this->warn("Invalid Shopify credentials for company: {$company->name}");
                continue; // Skip to the next company
            }

            $offset = 0;
            $productsSynced = 0;
            //$this->shopifyController->updateShopifyLocationTable();
            do {
                try {
                    $product = Product::whereNotNull('shopify_id')
                        ->where('company_id', $company->id)
                        ->orderBy('name')
                        ->skip($offset)
                        ->first();
                    
                    if ($product) {
                        $beforeSyncLevel = $product->current_stock_level;
                        $shopifyProduct = $this->shopifyController->getShopifyProduct($product->variants_id, $company->shopify_domain, $company->shopify_token);
            
                        if ($shopifyProduct && $shopifyProduct !== -1) {
                            foreach ($shopifyProduct['variants'] as $variant) {
                                if ($variant['id'] == $product->shopify_id) {
                                    $currentStockLevel = $variant['inventory_quantity'];
                                    $product->current_stock_level = $currentStockLevel;
                                    $product->touch();
                                    $product->save();
            
                                    foreach ($variant['inventory_levels'] as $inventoryLevel) {
                                        $locationId = $inventoryLevel['location_id'];
                                        $availableStockLevel = $inventoryLevel['available'];
                            
                                        // Find or create the product inventory record for the specific location
                                        $productInventory = ProductInventory::firstOrCreate(
                                            ['product_id' => $product->id, 'location_id' => $locationId],
                                            ['min_stock_level' => 0] // Default min_stock_level if not set
                                        );
                            
                                        // Update the current stock level in the inventory
                                        $productInventory->current_stock_level = $availableStockLevel;    
                                        $productInventory->save();
                                    }
            
                                    $productsSynced++;
                                    Log::info("{$product->name}: {$beforeSyncLevel} -> {$currentStockLevel} synced successfully.");
                                    $this->info("{$product->name}: {$beforeSyncLevel} -> {$currentStockLevel} synced successfully.");
                                }
                            }
                            $product->touch();
                            $offset++;
                            sleep(0.5);
                        } else {
                            if ($shopifyProduct === -1) {
                                sleep(1);
                            } else {
                                $this->info("Shopify product not found for product ID: {$product->id}");
                                Log::error("Shopify product not found for product ID: {$product->id}");
                                $product->touch();
                                $offset++;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // Log the error message
                    Log::error("Error syncing product at offset {$offset}: " . $e->getMessage());
                    $this->error("Error syncing product at offset {$offset}: " . $e->getMessage());
                }
            } while ($product);
            
        }

        $this->info("Products synced successfully. Total products synced: {$productsSynced}");
    }
}
