<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('signals', function (Blueprint $table) {
            $table->string('data_source')->nullable()->after('status');
            $table->string('data_status')->nullable()->after('data_source');
            $table->decimal('feed_price', 18, 5)->nullable()->after('data_status');
            $table->timestamp('generated_at')->nullable()->after('feed_price');
            $table->timestamp('expires_at')->nullable()->after('generated_at');
        });
    }

    public function down(): void
    {
        Schema::table('signals', function (Blueprint $table) {
            $table->dropColumn(['data_source', 'data_status', 'feed_price', 'generated_at', 'expires_at']);
        });
    }
};
