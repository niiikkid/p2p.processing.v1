<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_pings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_device_id')->constrained('user_devices')->cascadeOnDelete();
            $table->timestamp('pinged_at')->index();
            $table->index(['user_device_id', 'pinged_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_pings');
    }
};


