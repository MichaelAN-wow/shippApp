<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_products', function (Blueprint $table) {
            $table->id();
            $table->integer('sale_id');
            $table->integer('product_id')->nullable();
            $table->string('shopify_id')->nullable();
            $table->string('shopify_variants_id')->nullable();
            $table->string('shopify_product_id')->nullable();
            $table->string('product_name')->nullable()->comment('Added product name here because after considering sync case with Shopify');
            $table->decimal('quantity', 8, 3);
            $table->decimal('unit_price', 8, 3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sale_products');
    }
}
