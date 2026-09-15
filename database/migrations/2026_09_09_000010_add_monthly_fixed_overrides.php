<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fixed_expenses', function (Blueprint $table) {
            $table->decimal('amount', 12, 2)->nullable()->change();
        });

        Schema::table('fixed_expense_occurrences', function (Blueprint $table) {
            $table->decimal('amount', 12, 2)->nullable()->after('month');
            $table->string('detail')->nullable()->after('amount');
            $table->string('payment_type')->nullable()->after('detail');
            $table->text('notes')->nullable()->after('payment_type');
        });
    }

    public function down(): void
    {
        Schema::table('fixed_expense_occurrences', function (Blueprint $table) {
            $table->dropColumn(['amount', 'detail', 'payment_type', 'notes']);
        });

        Schema::table('fixed_expenses', function (Blueprint $table) {
            $table->decimal('amount', 12, 2)->nullable(false)->change();
        });
    }
};
