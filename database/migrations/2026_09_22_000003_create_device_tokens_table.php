<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('merchant_id')->constrained()->cascadeOnDelete();
            $table->char('token_hash', 64);
            $table->text('token');
            $table->string('platform', 20)->nullable();
            $table->timestamp('last_seen_at');
            $table->timestamps();
            $table->unique(['merchant_id', 'token_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_tokens');
    }
};
