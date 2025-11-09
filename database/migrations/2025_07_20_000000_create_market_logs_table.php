<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarketLogsTable extends Migration {
    public function up() {
        Schema::create('market_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('market_name');
            $table->date('date');
            $table->decimal('goal', 10, 2)->nullable();
            $table->decimal('sales', 10, 2)->nullable();
            $table->string('top_product')->nullable();
            $table->string('top_hour')->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('market_logs');
    }
}