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
        Schema::table('payment_gateways', function (Blueprint $table) {
            $table->boolean('has_sms_detail_pattern')->default(false)->after('make_order_amount_unique');
            $table->string('sms_detail_title')->nullable()->after('has_sms_detail_pattern');
            $table->string('sms_detail_pattern')->nullable()->after('sms_detail_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            $table->dropColumn([
                'has_sms_detail_pattern',
                'sms_detail_title',
                'sms_detail_pattern'
            ]);
        });
    }
};
