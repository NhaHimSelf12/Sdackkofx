<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('signals', function (Blueprint $table) {
            $table->boolean('is_primary')->default(false)->after('status');
        });
        Schema::table('markets', function (Blueprint $table) {
            $table->json('analysis_details')->nullable()->after('ai_summary');
        });
    }

    public function down(): void
    {
        Schema::table('signals', fn (Blueprint $table) => $table->dropColumn('is_primary'));
        Schema::table('markets', fn (Blueprint $table) => $table->dropColumn('analysis_details'));
    }
};
