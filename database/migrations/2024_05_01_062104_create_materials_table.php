<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('material_type')->nullable();
            $table->integer('unit_id')->nullable();
            $table->decimal('current_stock_level', 8,3);
            $table->decimal('min_stock_level', 8, 3)->nullable();
            $table->decimal('price_per_unit', 8, 3);
            $table->string('material_code')->nullable();
            $table->string('notes')->nullable();
            $table->string('photo_path')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->integer('category_id')->nullable();
            $table->dateTime('last_order_date')->nullable();
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
        //
        Schema::dropIfExists('materials');
    }
}
