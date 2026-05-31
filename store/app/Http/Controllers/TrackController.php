<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Shipping\AgenwebsiteClient;
use Illuminate\View\View;

class TrackController extends Controller
{
    public function show(string $orderNumber): View
    {
        $order = Order::where('order_number', $orderNumber)->first();

        $trackingHistory = null;

        if ($order && $order->shipping_resi && $order->shipping_courier) {
            $client = AgenwebsiteClient::fromConfig();
            $result = $client->tracking($order->shipping_resi, $order->shipping_courier);
            $trackingHistory = $result;
        }

        return view('pages.track', [
            'orderNumber' => $orderNumber,
            'dbOrder' => $order,
            'trackingHistory' => $trackingHistory,
        ]);
    }
}
