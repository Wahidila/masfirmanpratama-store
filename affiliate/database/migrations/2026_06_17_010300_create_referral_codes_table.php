<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliator_id')->constrained('affiliators')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('label')->nullable(); // custom label for affiliator
            $table->string('target_url')->nullable(); // specific product/page
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_codes');
    }
};
