<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ea_bots', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('mode'); // scalping | highlow | daytrade | swing
            $table->foreignId('market_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('capital', 10, 2);
            $table->decimal('risk_pct', 5, 2)->default(2);
            $table->string('status')->default('running'); // running | paused
            $table->unsignedInteger('positions_today')->default(0);
            $table->date('last_trade_date')->nullable();
            $table->unsignedInteger('trades')->default(0);
            $table->unsignedInteger('wins')->default(0);
            $table->unsignedInteger('losses')->default(0);
            $table->decimal('pnl', 12, 2)->default(0);
            $table->timestamp('last_run_at')->nullable();
            $table->timestamps();
        });

        Schema::create('ea_bot_trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ea_bot_id')->constrained()->cascadeOnDelete();
            $table->foreignId('market_id')->constrained()->cascadeOnDelete();
            $table->foreignId('signal_id')->nullable()->constrained()->nullOnDelete();
            $table->string('direction'); // buy | sell
            $table->decimal('entry', 14, 5);
            $table->decimal('stop_loss', 14, 5);
            $table->decimal('take_profit', 14, 5);
            $table->decimal('units', 16, 6);
            $table->decimal('risk_amount', 10, 2);
            $table->string('status')->default('open'); // open | won | lost | closed
            $table->decimal('pnl', 12, 2)->nullable();
            $table->string('note')->nullable();
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ea_bot_trades');
        Schema::dropIfExists('ea_bots');
    }
};
