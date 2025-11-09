<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->enum('sale_type', ['Retail', 'Whole Sale', 'Shopify']);
            $table->string('shopify_id')->nullable();
            $table->string('shopify_order_name')->nullable();
            $table->dateTime('sale_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->integer('customer_id')->nullable();
            $table->decimal('discount', 8, 3)->nullable()->default(0);
            $table->decimal('tax', 8, 3)->nullable()->default(0);
            $table->decimal('additional_charges', 8, 3)->nullable()->default(0);
            $table->string('notes')->nullable();
            $table->enum('shipping', ['Free Local Pickup', 'Shipped']);
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('country_code')->nullable();
            $table->integer('shipping_fee')->nullable()->default(0);

            //for demo
            $table->decimal('total', 10, 2)->nullable()->default(0);
            $table->string('status')->nullable()->default("Unpaid");
            $table->string('fulfillment_status')->nullable()->default("Unfulfilled");
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
        Schema::dropIfExists('sales');
    }
}
