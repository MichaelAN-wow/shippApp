<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('shipping_connections')) {
            Schema::create('shipping_connections', function (Blueprint $table) {
                $table->id();
                $table->string('carrier');               // UPS / FedEx / DHL ...
                $table->string('account_number');
                $table->string('api_key')->nullable();
                $table->string('api_secret')->nullable();
                $table->boolean('sandbox')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('shipping_connections');
    }
};
