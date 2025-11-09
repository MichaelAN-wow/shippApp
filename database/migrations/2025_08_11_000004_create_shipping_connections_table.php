<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_connections', function (Blueprint $table) {
            $table->id();
            $table->string('provider'); // e.g., easypost, shippo, dhl, ups
            $table->string('display_name')->nullable();
            $table->text('config')->nullable(); // JSON blob (store encrypted in production)
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_connections');
    }
};
