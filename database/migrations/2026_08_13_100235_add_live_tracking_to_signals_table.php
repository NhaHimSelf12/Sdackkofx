<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('signals', function (Blueprint $table) {
            $table->boolean('hit_entry')->default(false)->after('note');
            $table->boolean('hit_tp1')->default(false)->after('hit_entry');
            $table->boolean('hit_tp2')->default(false)->after('hit_tp1');
            $table->boolean('hit_tp')->default(false)->after('hit_tp2');
            $table->boolean('hit_sl')->default(false)->after('hit_tp');
            $table->boolean('is_closed')->default(false)->after('hit_sl');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('signals', function (Blueprint $table) {
            $table->dropColumn(['hit_entry', 'hit_tp1', 'hit_tp2', 'hit_tp', 'hit_sl', 'is_closed']);
        });
    }
};
