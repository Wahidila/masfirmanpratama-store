<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Data-fix: prepend 'storage/' to uploaded image paths that are missing the prefix.
 *
 * Seeder paths start with 'images/' (files directly in public/images/) — leave alone.
 * Paths already starting with 'storage/' — leave alone.
 * Everything else (e.g. 'products/slug/file.jpg', 'courses/slug/file.jpg') — prepend 'storage/'.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Fix products
        DB::table('products')
            ->whereNotNull('image_path')
            ->where('image_path', '!=', '')
            ->where('image_path', 'NOT LIKE', 'images/%')
            ->where('image_path', 'NOT LIKE', 'storage/%')
            ->update(['image_path' => DB::raw("'storage/' || image_path")]);

        // Fix courses
        DB::table('courses')
            ->whereNotNull('image_path')
            ->where('image_path', '!=', '')
            ->where('image_path', 'NOT LIKE', 'images/%')
            ->where('image_path', 'NOT LIKE', 'storage/%')
            ->update(['image_path' => DB::raw("'storage/' || image_path")]);
    }

    public function down(): void
    {
        // Strip 'storage/' prefix from paths that were fixed
        DB::table('products')
            ->whereNotNull('image_path')
            ->where('image_path', 'LIKE', 'storage/products/%')
            ->orWhere('image_path', 'LIKE', 'storage/courses/%')
            ->update(['image_path' => DB::raw('substr(image_path, 9)')]);

        DB::table('courses')
            ->whereNotNull('image_path')
            ->where('image_path', 'LIKE', 'storage/courses/%')
            ->update(['image_path' => DB::raw('substr(image_path, 9)')]);
    }
};
