<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('shipments', function (Blueprint $table) {
            $table->decimal('product_weight', 8, 2)->default(0);
            $table->decimal('total_weight', 8, 2)->default(0);
        });
    }

    public function down(): void {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['product_weight','total_weight']);
        });
    }
};
