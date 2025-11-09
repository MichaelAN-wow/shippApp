<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('supplier_id');
            $table->integer('status');
            $table->dateTime('received_at')->nullable();
            $table->decimal('discount', 10, 3)->nullable()->default(0);
            $table->decimal('tax', 10, 3)->nullable()->default(0);
            $table->decimal('shipping', 10, 3)->nullable()->default(0);
            $table->integer('order_number')->nullable();
            $table->string('notes')->nullable();

            //for demo
            $table->decimal('total', 15, 3)->nullable()->default(0);
            $table->integer('items')->nullable()->default(0);
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
        Schema::dropIfExists('orders');
    }
}
