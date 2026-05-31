<?php

namespace App\Services\Shipping;

use App\Models\Product;

class ShippingRateService
{
    public function __construct(private AgenwebsiteClient $agenwebsite) {}

    public function calculateWeight(array $cartItems): float
    {
        $slugs = array_column($cartItems, 'slug');
        if (empty($slugs)) {
            return 0.0;
        }

        $products = Product::whereIn('slug', $slugs)->get()->keyBy('slug');

        $total = 0.0;
        $hasShippable = false;

        foreach ($cartItems as $item) {
            $product = $products->get($item['slug']);
            if (!$product || $product->is_shippable === false) {
                continue;
            }
            $hasShippable = true;
            $weight = $product->weight_kg ?? config('shipping.default_weight_kg', 1);
            $total += $weight * $item['qty'];
        }

        if (!$hasShippable) {
            return 0.0;
        }

        return round(max($total, 1.0), 2);
    }

    public function calculateDimensions(array $cartItems): array
    {
        $slugs = array_column($cartItems, 'slug');
        $defaults = config('shipping.default_dimensions_cm', ['length' => 10, 'width' => 10, 'height' => 5]);

        if (empty($slugs)) {
            return $this->castDefaults($defaults);
        }

        $products = Product::whereIn('slug', $slugs)->get()->keyBy('slug');

        $hasLength = false;
        $hasWidth = false;
        $hasHeight = false;
        $maxLength = 0;
        $maxWidth = 0;
        $totalHeight = 0;

        foreach ($cartItems as $item) {
            $product = $products->get($item['slug']);
            if (!$product || $product->is_shippable === false) {
                continue;
            }

            if ($product->length_cm !== null) {
                $hasLength = true;
                $maxLength = max($maxLength, $product->length_cm);
            }
            if ($product->width_cm !== null) {
                $hasWidth = true;
                $maxWidth = max($maxWidth, $product->width_cm);
            }
            if ($product->height_cm !== null) {
                $hasHeight = true;
                $totalHeight += $product->height_cm * $item['qty'];
            }
        }

        return [
            'length' => (int) ($hasLength ? $maxLength : $defaults['length']),
            'width' => (int) ($hasWidth ? $maxWidth : $defaults['width']),
            'height' => (int) ($hasHeight ? $totalHeight : $defaults['height']),
        ];
    }

    private function castDefaults(array $defaults): array
    {
        return [
            'length' => (int) ($defaults['length'] ?? 10),
            'width' => (int) ($defaults['width'] ?? 10),
            'height' => (int) ($defaults['height'] ?? 5),
        ];
    }
}
