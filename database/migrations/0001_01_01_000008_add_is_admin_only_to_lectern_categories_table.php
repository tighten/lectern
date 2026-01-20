<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('lectern_categories', 'is_admin_only')) {
            return;
        }

        Schema::table('lectern_categories', function (Blueprint $table) {
            $table->boolean('is_admin_only')->default(false)->after('is_private');
        });
    }

    public function down(): void
    {
        Schema::table('lectern_categories', function (Blueprint $table) {
            $table->dropColumn('is_admin_only');
        });
    }
};
