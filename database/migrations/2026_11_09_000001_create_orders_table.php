<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('merchant_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('salla_id');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->json('status')->nullable();
            $table->string('currency')->nullable();
            $table->string('payment_method')->nullable();
            foreach (['amounts', 'customer', 'items', 'shipping', 'shipments', 'urls', 'status_update'] as $field) {
                $table->json($field)->nullable();
            }
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
        Schema::dropIfExists('orders');
    }
};
