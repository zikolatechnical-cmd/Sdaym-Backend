<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('merchant_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('salla_id');
            $table->string('name');
            $table->string('sku')->nullable();
            $table->string('type')->nullable();
            $table->string('status')->nullable();
            $table->boolean('is_available')->default(false);
            $table->boolean('show_in_app')->default(false);
            $table->decimal('quantity', 18, 4)->nullable();
            $table->text('description')->nullable();
            $table->text('main_image')->nullable();
            foreach (['price', 'sale_price', 'regular_price', 'urls', 'images', 'options', 'skus', 'categories', 'brand', 'tags', 'booking_details'] as $field) {
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
        Schema::dropIfExists('products');
    }
};
