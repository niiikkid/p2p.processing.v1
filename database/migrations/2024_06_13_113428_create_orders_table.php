<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable();
            $table->string('amount')->nullable();
            $table->string('profit')->nullable();
            $table->string('currency')->nullable();
            $table->string('profit_currency')->nullable();
            $table->string('conversion_price')->nullable();
            $table->string('conversion_price_with_commission')->nullable();
            $table->float('commission_rate', 2)->nullable();
            $table->string('status')->nullable();
            $table->longText('callback_url')->nullable();
            $table->foreignId('payment_gateway_id')->nullable();
            $table->foreignId('payment_detail_id')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
