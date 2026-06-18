<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliator_type_id')->nullable()->constrained('affiliator_types');
            $table->string('product_type')->nullable(); // book, course, or null=global
            $table->decimal('rate', 5, 2); // percentage
            $table->decimal('min_amount', 12, 2)->default(0);
            $table->integer('cooling_days')->default(7);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_settings');
    }
};
