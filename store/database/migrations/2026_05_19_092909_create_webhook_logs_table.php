<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('direction', ['in', 'out'])->index();
            $table->string('event')->index();
            $table->json('payload')->nullable();
            $table->enum('status', ['success', 'failed', 'pending'])->default('pending')->index();
            $table->integer('response_code')->nullable();
            $table->integer('attempt')->default(1);
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_logs');
    }
};
