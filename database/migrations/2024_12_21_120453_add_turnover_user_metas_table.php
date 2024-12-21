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
            $table->longText('payment_detail_turnover')->nullable()->after('user_id');
        });

        \App\Models\UserMeta::query()->update(['payment_detail_turnover' => []]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_metas', function (Blueprint $table) {
            $table->dropColumn('payment_detail_turnover');
        });
    }
};
