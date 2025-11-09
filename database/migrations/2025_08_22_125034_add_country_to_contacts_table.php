<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (!Schema::hasColumn('contacts', 'country')) {
                $table->string('country', 2)->default('US')->after('zip');
            }
        });
    }


    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('country');
        });
    }
};
