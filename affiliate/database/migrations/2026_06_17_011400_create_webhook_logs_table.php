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
            $table->string('event_type'); // order-paid, order-refunded
            $table->json('payload');
            $table->string('signature')->nullable();
            $table->enum('status', ['received', 'processed', 'failed', 'invalid_signature'])->default('received');
            $table->text('error_message')->nullable();
            $table->string('source_ip', 45)->nullable();
            $table->timestamps();
            $table->index('event_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_logs');
    }
};
