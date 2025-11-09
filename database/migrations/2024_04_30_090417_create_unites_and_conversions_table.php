<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitesAndConversionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create units table
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name');
            $table->enum('type', ['Quantity', 'Weight', 'Length', 'Area', 'Volume']);
            $table->decimal('conversion_factor', 10, 6)->default(1);
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
        // Drop conversions table
        Schema::dropIfExists('conversions');

        // Drop units table
        Schema::dropIfExists('units');
    }
}
