<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaterialQuantityToProductionsProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('production_products', function (Blueprint $table) {
            // Add the material_quantity column next to the quantity column and make it nullable
            $table->decimal('material_quantity', 5, 2)->nullable()->after('quantity');
        });
    }

    public function down()
    {
        Schema::table('production_products', function (Blueprint $table) {
            // Drop the material_quantity column if we rollback the migration
            $table->dropColumn('material_quantity');
        });
    }
}
