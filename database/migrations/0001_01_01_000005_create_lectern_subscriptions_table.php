<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lectern_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->morphs('subscribable');
            $table->timestamp('created_at')->nullable();

            $table->unique(['user_id', 'subscribable_id', 'subscribable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lectern_subscriptions');
    }
};
