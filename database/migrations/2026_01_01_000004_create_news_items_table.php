<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('news_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('source')->nullable();
            $table->string('url')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('sentiment')->default('neutral'); // bullish | bearish | neutral
            $table->string('impact')->default('medium');     // high | medium | low
            $table->text('summary')->nullable();             // AI-generated summary
            $table->json('symbols')->nullable();             // affected symbols ["XAUUSD", ...]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_items');
    }
};
