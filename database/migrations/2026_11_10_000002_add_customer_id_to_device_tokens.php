<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('device_tokens', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->index(['merchant_id', 'customer_id']);
        });
    }

    public function down(): void
    {
        Schema::table('device_tokens', function (Blueprint $table) {
            $table->dropIndex(['merchant_id', 'customer_id']);
            $table->dropColumn('customer_id');
        });
    }
};
