<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('weight_kg', 8, 2)->nullable()->after('meta_seo');
            $table->unsignedInteger('length_cm')->nullable()->after('weight_kg');
            $table->unsignedInteger('width_cm')->nullable()->after('length_cm');
            $table->unsignedInteger('height_cm')->nullable()->after('width_cm');
            $table->boolean('is_shippable')->default(true)->after('height_cm');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['weight_kg', 'length_cm', 'width_cm', 'height_cm', 'is_shippable']);
        });
    }
};
