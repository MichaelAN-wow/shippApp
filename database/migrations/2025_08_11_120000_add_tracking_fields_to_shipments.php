<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'last_tracked_at')) {
                $table->timestamp('last_tracked_at')->nullable();
            }
            if (!Schema::hasColumn('shipments', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable();
            }
            if (!Schema::hasColumn('shipments', 'status_code')) {
                $table->string('status_code')->nullable();
            }
            if (!Schema::hasColumn('shipments', 'status_description')) {
                $table->string('status_description')->nullable();
            }
            if (!Schema::hasColumn('shipments', 'raw_tracking')) {
                $table->json('raw_tracking')->nullable();
            }
            if (!Schema::hasColumn('shipments', 'label_path')) {
                $table->string('label_path')->nullable();
            }
        });
    }

    public function down(): void {
        Schema::table('shipments', function (Blueprint $table) {
            $cols = [
                'last_tracked_at',
                'delivered_at',
                'status_code',
                'status_description',
                'raw_tracking',
                'label_path',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('shipments', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
