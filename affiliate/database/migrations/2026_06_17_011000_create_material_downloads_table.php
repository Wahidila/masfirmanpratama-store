<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('materials')->cascadeOnDelete();
            $table->foreignId('affiliator_id')->constrained('affiliators')->cascadeOnDelete();
            $table->timestamp('downloaded_at');
            $table->index(['material_id', 'affiliator_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_downloads');
    }
};
