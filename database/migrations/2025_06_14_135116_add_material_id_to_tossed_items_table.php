<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaterialIdToTossedItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tossed_items', function (Blueprint $table) {
            if (!Schema::hasColumn('tossed_items', 'material_id')) {
                $table->unsignedBigInteger('material_id')->after('user_id');
            }

            if (!Schema::hasColumn('tossed_items', 'quantity')) {
                $table->integer('quantity')->default(0)->after('material_id');
            }

            if (!Schema::hasColumn('tossed_items', 'reason')) {
                $table->string('reason', 255)->nullable()->after('quantity');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tossed_items', function (Blueprint $table) {
            $table->dropColumn(['material_id', 'quantity', 'reason']);
        });
    }
}