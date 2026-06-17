<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['challenge', 'contest', 'bonus'])->default('challenge');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->json('rules')->nullable();
            $table->json('rewards')->nullable(); // [{rank, prize, description}]
            $table->enum('status', ['draft', 'active', 'ended'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_events');
    }
};
