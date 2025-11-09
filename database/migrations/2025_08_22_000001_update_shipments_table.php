<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'sender_id')) {
                $table->unsignedBigInteger('sender_id')->nullable()->after('order_id');
            }
            if (!Schema::hasColumn('shipments', 'receiver_id')) {
                $table->unsignedBigInteger('receiver_id')->nullable()->after('sender_id');
            }

            if (Schema::hasColumn('shipments', 'contact_id')) {
                $table->dropForeign(['contact_id']);
                $table->dropColumn('contact_id');
            }

            if (!Schema::hasColumn('shipments', 'carrier')) {
                $table->string('carrier')->default('UPS');
            }
            if (!Schema::hasColumn('shipments', 'tracking_number')) {
                $table->string('tracking_number')->nullable()->unique();
            }
            if (!Schema::hasColumn('shipments', 'label_path')) {
                $table->string('label_path')->nullable();
            }
            if (!Schema::hasColumn('shipments', 'status')) {
                $table->enum('status', ['pending', 'in_transit', 'delivered'])->default('in_transit');
            }
        });

        Schema::table('shipments', function (Blueprint $table) {
            if (Schema::hasColumn('shipments', 'sender_id')) {
                $table->foreign('sender_id')->references('id')->on('contacts')->onDelete('cascade');
            }
            if (Schema::hasColumn('shipments', 'receiver_id')) {
                $table->foreign('receiver_id')->references('id')->on('contacts')->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        Schema::table('shipments', function (Blueprint $table) {
            if (Schema::hasColumn('shipments', 'sender_id')) {
                $table->dropForeign(['sender_id']);
                $table->dropColumn('sender_id');
            }
            if (Schema::hasColumn('shipments', 'receiver_id')) {
                $table->dropForeign(['receiver_id']);
                $table->dropColumn('receiver_id');
            }
            if (Schema::hasColumn('shipments', 'carrier')) {
                $table->dropColumn('carrier');
            }
            if (Schema::hasColumn('shipments', 'tracking_number')) {
                $table->dropColumn('tracking_number');
            }
            if (Schema::hasColumn('shipments', 'label_path')) {
                $table->dropColumn('label_path');
            }
            if (Schema::hasColumn('shipments', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
