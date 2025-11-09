<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaterialIdToProductionsProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('production_products', function (Blueprint $table) {
            // Adding the material_id column next to product_id
            $table->unsignedBigInteger('material_id')->nullable()->after('product_id');
        });
    }

    public function down()
    {
        Schema::table('production_products', function (Blueprint $table) {
            // Dropping the material_id column if we rollback the migration
            $table->dropColumn('material_id');
        });
    }
}
