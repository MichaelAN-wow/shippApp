<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCostToShipmentsTable extends Migration
{
 
    public function up()
    {
        Schema::table('shipments', function (Blueprint $table) {
            
            $table->decimal('cost', 10, 2)->nullable()->after('label_path');
        });
    }


    public function down()
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn('cost');
        });
    }
}
