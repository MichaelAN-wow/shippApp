<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (!Schema::hasColumn('contacts', 'street')) {
                $table->string('street')->nullable();
            }
            if (!Schema::hasColumn('contacts', 'city')) {
                $table->string('city')->nullable();
            }
            if (!Schema::hasColumn('contacts', 'state')) {
                $table->string('state')->nullable();
            }
            if (!Schema::hasColumn('contacts', 'zip')) {
                $table->string('zip')->nullable();
            }
            if (!Schema::hasColumn('contacts', 'country')) {
                $table->string('country')->nullable();
            }
        });
    }


    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn(['street', 'city', 'state', 'zip', 'country']);
        });
    }
};
