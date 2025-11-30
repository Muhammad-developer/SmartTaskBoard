<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['created', 'updated', 'assigned', 'commented', 'mentioned']);
            $table->unsignedBigInteger('notifiable_id');
            $table->string('notifiable_type');
            $table->timestamp('read_at')->nullable();
            $table->timestamp('created_at');
            $table->index('user_id');
            $table->index('actor_id');
            $table->index(['notifiable_type', 'notifiable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
