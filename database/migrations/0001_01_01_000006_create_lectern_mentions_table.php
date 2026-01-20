<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lectern_mentions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('lectern_posts')->cascadeOnDelete();
            $table->foreignId('user_id')->index();
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lectern_mentions');
    }
};
