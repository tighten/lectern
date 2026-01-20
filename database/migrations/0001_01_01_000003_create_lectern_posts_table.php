<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lectern_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained('lectern_threads')->cascadeOnDelete();
            $table->foreignId('user_id')->index();
            $table->foreignId('parent_id')->nullable()->constrained('lectern_posts')->nullOnDelete();
            $table->text('body');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lectern_posts');
    }
};
