<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trendlines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('market_id')->constrained()->cascadeOnDelete();
            $table->string('kind');                 // support | resistance | trend
            $table->string('direction');            // up (buy side) | down (sell side) | flat
            $table->string('timeframe')->default('H1');
            $table->unsignedBigInteger('start_time'); // unix seconds
            $table->decimal('start_price', 18, 5);
            $table->unsignedBigInteger('end_time');   // unix seconds
            $table->decimal('end_price', 18, 5);
            $table->unsignedTinyInteger('touches')->default(2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trendlines');
    }
};
