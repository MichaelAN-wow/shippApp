<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            if (!Schema::hasColumn('boxes', 'length')) {
                $table->decimal('length', 8, 2)->nullable()->after('name');
            }
            if (!Schema::hasColumn('boxes', 'height')) {
                $table->decimal('height', 8, 2)->nullable()->after('length');
            }
            if (!Schema::hasColumn('boxes', 'width')) {
                $table->decimal('width', 8, 2)->nullable()->after('height');
            }
            if (!Schema::hasColumn('boxes', 'weight')) {
                $table->decimal('weight', 8, 2)->nullable()->after('width');
            }
            if (!Schema::hasColumn('boxes', 'supplier')) {
                $table->string('supplier')->nullable()->after('quantity');
            }
            if (!Schema::hasColumn('boxes', 'cost')) {
                $table->decimal('cost', 10, 2)->nullable()->after('supplier');
            }
        });
    }

    public function down(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            $table->dropColumn(['length', 'height', 'width', 'weight', 'supplier', 'cost']);
        });
    }
};
