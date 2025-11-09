<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'estimated_delivery')) {
                $table->date('estimated_delivery')->nullable();
            }
            if (!Schema::hasColumn('shipments', 'last_location')) {
                $table->string('last_location')->nullable();
            }
        });
    }

    public function down(): void {
        Schema::table('shipments', function (Blueprint $table) {
            if (Schema::hasColumn('shipments', 'estimated_delivery')) {
                $table->dropColumn('estimated_delivery');
            }
            if (Schema::hasColumn('shipments', 'last_location')) {
                $table->dropColumn('last_location');
            }
        });
    }
};
