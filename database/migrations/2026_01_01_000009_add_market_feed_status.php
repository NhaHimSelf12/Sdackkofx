<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('markets', function (Blueprint $table) {
            $table->string('data_source')->default('demo')->after('change_pct');
            $table->string('data_status')->default('demo')->after('data_source'); // live, delayed, stale, demo
            $table->timestamp('price_fetched_at')->nullable()->after('data_status');
            $table->text('feed_error')->nullable()->after('price_fetched_at');
        });
    }

    public function down(): void
    {
        Schema::table('markets', function (Blueprint $table) {
            $table->dropColumn(['data_source', 'data_status', 'price_fetched_at', 'feed_error']);
        });
    }
};
