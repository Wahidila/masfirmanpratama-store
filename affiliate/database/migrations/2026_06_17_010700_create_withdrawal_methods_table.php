<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawal_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // BCA, BNI, Mandiri, Dana, OVO, etc
            $table->string('type'); // bank_transfer, e_wallet
            $table->boolean('is_active')->default(true);
            $table->decimal('min_withdrawal', 12, 2)->default(50000);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawal_methods');
    }
};
