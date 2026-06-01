<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * B1: added course_id for course-specific installment schemes.
     * A scheme targets EITHER a product OR a course (or neither = global).
     */
    public function up(): void
    {
        Schema::create('installment_schemes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->string('name');
            $table->decimal('dp_pct', 5, 2);
            $table->integer('n_installments');
            $table->integer('interval_days');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installment_schemes');
    }
};
