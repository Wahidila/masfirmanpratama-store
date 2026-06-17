<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_event_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_event_id')->constrained('affiliate_events')->cascadeOnDelete();
            $table->foreignId('affiliator_id')->constrained('affiliators');
            $table->string('reward_type'); // cash, voucher, badge, bonus_commission
            $table->decimal('reward_value', 12, 2)->default(0);
            $table->string('description')->nullable();
            $table->boolean('is_claimed')->default(false);
            $table->timestamp('claimed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_event_rewards');
    }
};
