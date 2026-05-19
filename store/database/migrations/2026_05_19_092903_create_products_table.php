<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->enum('type', ['book', 'course']);
            $table->string('title');
            $table->decimal('price', 12, 2);
            $table->integer('stock')->default(0);
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft')->index();
            $table->string('image_path')->nullable();
            $table->text('description')->nullable();
            $table->json('meta_seo')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
