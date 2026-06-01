<?php

namespace App\Http\Controllers;

use App\Exceptions\ShippingRateException;
use App\Services\Shipping\ShippingRateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ShippingRateController extends Controller
{
    public function __construct(private ShippingRateService $shippingRateService) {}

    public function rates(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'city' => ['required', 'string'],
                'province' => ['required', 'string'],
                'zipcode' => ['required', 'string'],
                'cart_json' => ['required', 'string'],
            ]);

            $cart = json_decode($validated['cart_json'], true);

            if (! is_array($cart) || empty($cart)) {
                throw ValidationException::withMessages([
                    'cart_json' => 'Cart tidak valid atau kosong.',
                ]);
            }

            $destination = [
                'province' => $validated['province'],
                'city' => $validated['city'],
                'district' => $request->input('district', ''),
                'zipcode' => $validated['zipcode'],
            ];

            $cartItems = array_map(fn ($item) => [
                'slug' => $item['slug'] ?? '',
                'qty' => (int) ($item['qty'] ?? 1),
            ], $cart);

            $rates = $this->shippingRateService->getRates($destination, $cartItems);

            return response()->json(['rates' => $rates]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (ShippingRateException $e) {
            return response()->json([
                'rates' => [],
                'error' => 'Ongkir sementara tidak tersedia. Silakan hubungi admin via WhatsApp untuk konfirmasi ongkir.',
            ], 200);
        } catch (\Throwable $e) {
            return response()->json(['rates' => []]);
        }
    }
}
