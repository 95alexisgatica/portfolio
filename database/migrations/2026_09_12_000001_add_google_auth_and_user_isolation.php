<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('fixed_expense_occurrences')->delete();
        DB::table('fixed_expenses')->delete();
        DB::table('expenses')->delete();
        DB::table('expense_categories')->delete();
        DB::table('monthly_incomes')->delete();
        DB::table('monthly_inspirations')->delete();
        DB::table('comments')->delete();
        DB::table('sessions')->delete();
        DB::table('users')->delete();

        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable()->unique();
            $table->string('password')->nullable()->change();
            $table->string('nickname')->nullable();
            $table->string('avatar_path')->nullable();
            $table->string('role')->default('user');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        });

        Schema::table('expense_categories', function (Blueprint $table) {
            $table->dropUnique(['name']);
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unique(['user_id', 'name']);
        });

        Schema::table('monthly_incomes', function (Blueprint $table) {
            $table->dropUnique(['year', 'month']);
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unique(['user_id', 'year', 'month']);
        });

        Schema::table('monthly_inspirations', function (Blueprint $table) {
            $table->dropUnique(['year', 'month']);
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unique(['user_id', 'year', 'month']);
        });

        Schema::table('fixed_expenses', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
        Schema::table('fixed_expenses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
        Schema::table('monthly_inspirations', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'year', 'month']);
            $table->dropConstrainedForeignId('user_id');
            $table->unique(['year', 'month']);
        });
        Schema::table('monthly_incomes', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'year', 'month']);
            $table->dropConstrainedForeignId('user_id');
            $table->unique(['year', 'month']);
        });
        Schema::table('expense_categories', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'name']);
            $table->dropConstrainedForeignId('user_id');
            $table->unique(['name']);
        });
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['google_id']);
            $table->dropColumn(['google_id', 'nickname', 'avatar_path', 'role']);
            $table->string('password')->nullable(false)->change();
        });
    }
};
