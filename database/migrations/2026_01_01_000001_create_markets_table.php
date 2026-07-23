<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('markets', function (Blueprint $table) {
            $table->id();
            $table->string('symbol')->unique();          // XAUUSD, BTCUSD, EURUSD...
            $table->string('name');
            $table->string('category')->default('forex'); // forex | metals | crypto | indices
            $table->decimal('price', 18, 5)->default(0);
            $table->decimal('change_pct', 8, 3)->default(0);
            $table->string('ai_bias')->nullable();        // bullish | bearish | neutral
            $table->unsignedTinyInteger('ai_confidence')->default(0); // 0-100
            $table->text('ai_summary')->nullable();
            $table->json('key_levels')->nullable();       // ["support" => [...], "resistance" => [...]]
            $table->timestamp('analyzed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('markets');
    }
};
