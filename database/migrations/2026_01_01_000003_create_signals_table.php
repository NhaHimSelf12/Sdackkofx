<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('signals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('market_id')->constrained()->cascadeOnDelete();
            $table->string('strategy');                    // SMC | ICT | MSNR | ...
            $table->string('direction');                   // buy | sell
            $table->string('timeframe')->default('H1');
            $table->decimal('entry', 18, 5);
            $table->decimal('stop_loss', 18, 5);
            $table->decimal('take_profit', 18, 5);
            $table->decimal('risk_reward', 6, 2)->default(0);
            $table->unsignedTinyInteger('confidence')->default(0); // 0-100
            $table->string('status')->default('active');   // active | won | lost | expired
            $table->text('note')->nullable();              // reasoning behind the signal
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signals');
    }
};
