<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lectern_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('lectern_posts')->cascadeOnDelete();
            $table->foreignId('user_id')->index();
            $table->string('type');
            $table->timestamp('created_at')->nullable();

            $table->unique(['post_id', 'user_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lectern_reactions');
    }
};
