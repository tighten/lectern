<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lectern_threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('lectern_categories')->cascadeOnDelete();
            $table->foreignId('user_id')->index();
            $table->string('title');
            $table->string('slug');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id', 'is_pinned', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lectern_threads');
    }
};
