<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ea_bots', function (Blueprint $table) {
            $table->string('last_note', 220)->nullable()->after('last_run_at');
        });
    }

    public function down(): void
    {
        Schema::table('ea_bots', function (Blueprint $table) {
            $table->dropColumn('last_note');
        });
    }
};
