<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fixed_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('category')->nullable();
            $table->string('detail')->nullable();
            $table->string('payment_type');
            $table->text('notes')->nullable();
            $table->decimal('amount', 12, 2);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('fixed_expense_occurrences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixed_expense_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->string('status')->default('unpaid');
            $table->boolean('excluded')->default(false);
            $table->timestamps();
            $table->unique(['fixed_expense_id', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixed_expense_occurrences');
        Schema::dropIfExists('fixed_expenses');
    }
};
