<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monthly_inspirations', function (Blueprint $table) {
            $table->text('image_url')->nullable()->after('path');
            $table->string('path')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('monthly_inspirations', function (Blueprint $table) {
            $table->dropColumn('image_url');
            $table->string('path')->nullable(false)->change();
        });
    }
};
