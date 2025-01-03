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
        \App\Models\UserMeta::all()->each(function (\App\Models\UserMeta $meta) {
            $serviceCommissions = $meta->service_commissions;
            foreach ($serviceCommissions as $merchantID => $merchantCommissions) {
                foreach ($merchantCommissions as $gatewayID => $commission) {
                    $serviceCommissions[$merchantID][$gatewayID] = [
                        'gateway_total_commission' => null,
                        'merchant_commission' => $commission,
                    ];
                }
            }
            $meta->update(['service_commissions' => $serviceCommissions]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
