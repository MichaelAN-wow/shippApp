<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            
            if (Schema::hasColumn('boxes', 'weight') && !Schema::hasColumn('boxes', 'empty_weight')) {
                $table->renameColumn('weight', 'empty_weight');
            }

            
            if (!Schema::hasColumn('boxes', 'empty_weight')) {
                $table->decimal('empty_weight', 8, 2)->nullable()->after('width');
            }
        });

        
        DB::table('boxes')->whereNull('empty_weight')->update(['empty_weight' => 0]);
    }

    public function down(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            
            if (Schema::hasColumn('boxes', 'empty_weight') && !Schema::hasColumn('boxes', 'weight')) {
                $table->renameColumn('empty_weight', 'weight');
            }
        });
    }
};
