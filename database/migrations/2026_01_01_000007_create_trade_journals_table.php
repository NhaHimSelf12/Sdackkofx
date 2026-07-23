<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trade_journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('market_id')->constrained()->cascadeOnDelete();
            $table->foreignId('signal_id')->nullable()->constrained()->nullOnDelete();
            $table->string('direction');
            $table->string('strategy')->nullable();
            $table->string('timeframe')->default('H1');
            $table->decimal('entry', 18, 5);
            $table->decimal('stop_loss', 18, 5);
            $table->decimal('take_profit', 18, 5)->nullable();
            $table->decimal('exit_price', 18, 5)->nullable();
            $table->decimal('lot_size', 12, 3)->default(0.01);
            $table->decimal('risk_amount', 15, 2)->default(0);
            $table->decimal('profit_loss', 15, 2)->nullable();
            $table->decimal('r_multiple', 8, 2)->nullable();
            $table->string('status')->default('planned'); // planned, open, won, lost, breakeven, cancelled
            $table->unsignedTinyInteger('emotion_before')->nullable();
            $table->unsignedTinyInteger('execution_score')->nullable();
            $table->text('setup_notes')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trade_journals');
    }
};
