<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fixed_expenses', function (Blueprint $table) {
            $table->date('starts_at')->nullable()->after('amount');
            $table->date('ends_at')->nullable()->after('starts_at');
        });

        DB::statement("UPDATE fixed_expenses SET starts_at = date(created_at) WHERE starts_at IS NULL");
    }

    public function down(): void
    {
        Schema::table('fixed_expenses', function (Blueprint $table) {
            $table->dropColumn(['starts_at', 'ends_at']);
        });
    }
};
