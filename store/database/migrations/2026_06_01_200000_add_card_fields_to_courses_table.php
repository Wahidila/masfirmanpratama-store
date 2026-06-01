<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->integer('sort_order')->default(0)->after('meta_seo');
            $table->boolean('show_on_homepage')->default(true)->after('sort_order');
            $table->json('card_features')->nullable()->after('show_on_homepage');
            $table->string('card_icon')->nullable()->after('card_features');
            $table->string('card_icon_color')->nullable()->after('card_icon');
            $table->string('card_style')->default('default')->after('card_icon_color');
            $table->string('cta_label')->nullable()->after('card_style');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'sort_order',
                'show_on_homepage',
                'card_features',
                'card_icon',
                'card_icon_color',
                'card_style',
                'cta_label',
            ]);
        });
    }
};
