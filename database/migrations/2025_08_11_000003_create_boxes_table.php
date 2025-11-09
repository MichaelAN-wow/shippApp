<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('boxes')) {
            Schema::create('boxes', function (Blueprint $table) {
                $table->id();
                $table->string('name');                 
                $table->decimal('length', 8, 2)->nullable();
                $table->decimal('height', 8, 2)->nullable();
                $table->decimal('width', 8, 2)->nullable();
                $table->decimal('weight', 8, 2)->nullable(); 
                $table->integer('quantity')->default(0);     
                $table->string('supplier')->nullable();
                $table->decimal('cost', 10, 2)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('boxes');
    }
};
