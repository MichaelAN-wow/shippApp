<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('batch_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('folder')->default('General');
            $table->json('tags')->nullable();
            $table->text('content')->nullable();
            $table->string('color')->nullable();
            $table->integer('x')->default(0);
            $table->integer('y')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('batch_notes');
    }
};
