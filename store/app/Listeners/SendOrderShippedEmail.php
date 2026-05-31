<?php

namespace App\Listeners;

use App\Events\OrderShipped;
use App\Mail\OrderShippedMail;
use Illuminate\Support\Facades\Mail;

class SendOrderShippedEmail
{
    public function handle(OrderShipped $event): void
    {
        $order = $event->order;

        if ($order->shipped_email_sent_at !== null) {
            return;
        }

        if (empty($order->shipping_resi)) {
            return;
        }

        if (empty($order->email)) {
            return;
        }

        Mail::to($order->email)->send(new OrderShippedMail($order));

        $order->shipped_email_sent_at = now();
        $order->save();
    }
}
