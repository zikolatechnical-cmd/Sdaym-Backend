<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('special_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('merchant_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('salla_id');
            $table->string('name');
            $table->text('message')->nullable();
            $table->timestamp('expiry_date')->nullable();
            $table->string('offer_type')->nullable();
            $table->string('status')->nullable();
            $table->json('buy')->nullable();
            $table->json('get')->nullable();
            $table->json('raw_data');
            $table->timestamp('salla_created_at')->nullable();
            $table->timestamp('salla_updated_at')->nullable();
            $table->timestamp('notification_sent_at')->nullable();
            $table->timestamps();
            $table->unique(['merchant_id', 'salla_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('special_offers');
    }
};
