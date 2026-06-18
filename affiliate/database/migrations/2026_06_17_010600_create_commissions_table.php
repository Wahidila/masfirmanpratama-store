<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliator_id')->constrained('affiliators');
            $table->foreignId('referral_order_id')->constrained('referral_orders');
            $table->decimal('amount', 12, 2);
            $table->decimal('rate_applied', 5, 2);
            $table->enum('status', ['cooling', 'available', 'withdrawn', 'cancelled'])->default('cooling');
            $table->timestamp('available_at'); // after cooling period
            $table->timestamp('withdrawn_at')->nullable();
            $table->timestamps();
            $table->index(['affiliator_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
