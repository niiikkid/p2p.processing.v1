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
        Schema::table('user_metas', function (Blueprint $table) {
            $table->longText('exchange_markup_rates')->nullable()->after('payment_detail_turnover');
        });

        \App\Models\UserMeta::query()->update([
            'exchange_markup_rates' => []
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_metas', function (Blueprint $table) {
            $table->dropColumn('exchange_markup_rates');
        });
    }
};
