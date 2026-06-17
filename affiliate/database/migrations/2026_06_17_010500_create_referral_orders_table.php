<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_code_id')->constrained('referral_codes');
            $table->foreignId('affiliator_id')->constrained('affiliators');
            $table->string('store_order_id'); // from webhook
            $table->string('buyer_name');
            $table->decimal('order_total', 12, 2);
            $table->enum('status', ['pending', 'paid', 'refunded'])->default('pending');
            $table->timestamp('ordered_at');
            $table->timestamps();
            $table->index('store_order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_orders');
    }
};
