<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('box_id')->nullable();
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('receiver_id');
            $table->decimal('product_weight', 8, 2)->default(0);
            $table->decimal('total_weight', 8, 2)->default(0);
            $table->string('carrier')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('label_path')->nullable();
            $table->string('status')->default('in_transit');
            $table->timestamps();

            $table->foreign('box_id')->references('id')->on('boxes')->nullOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('shipments');
    }
};
