<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaterialUnitIdToProductionProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('production_products', function (Blueprint $table) {
            $table->unsignedBigInteger('material_unit_id')->nullable()->after('material_quantity');
            $table->foreign('material_unit_id')->references('id')->on('units')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('production_products', function (Blueprint $table) {
            $table->dropForeign(['material_unit_id']);
            $table->dropColumn('material_unit_id');
        });
    }
}
