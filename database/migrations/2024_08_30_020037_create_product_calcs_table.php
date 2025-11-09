<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCalcsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_calcs', function (Blueprint $table) {
            $table->id();
            $table->json('headers'); // To store table headers as JSON
            $table->json('data'); // To store table data as JSON
            $table->json('formulas'); // Store formulas as JSON
            $table->unsignedBigInteger('unit_id'); // Store unit ID
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
        Schema::dropIfExists('product_calcs');
    }
}