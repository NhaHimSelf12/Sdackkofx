<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('trader')->after('password');
            $table->string('timezone')->default('Asia/Bangkok');
            $table->decimal('account_balance', 15, 2)->default(10000);
            $table->decimal('default_risk_pct', 5, 2)->default(1);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'timezone', 'account_balance', 'default_risk_pct']);
        });
    }
};
