<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('shopify_id')->nullable(); // Adding Shopify product ID
            $table->string('variants_id')->nullable();
            $table->string('name');
            $table->enum('product_type', ['Sourced from Supplier', 'Made By Me'])->default('Sourced from Supplier');
            
            $table->integer('current_stock_level');
            $table->integer('min_stock_level')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('unit_id');
            $table->integer('category_id')->nullable();
            $table->integer('batch_size')->default(1);
            $table->string('photo_path')->nullable();
            $table->string('product_code')->nullable();
            $table->string('variants_name')->nullable();
            $table->string('notes')->nullable();
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
        Schema::dropIfExists('products');
    }
}
