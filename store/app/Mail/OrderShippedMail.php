<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class OrderShippedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pesanan #'.$this->order->order_number.' telah dikirim',
        );
    }

    public function content(): Content
    {
        $ttlDays = (int) config('checkout.track_url_ttl_days', 30);

        return new Content(
            view: 'emails.order-shipped',
            with: [
                'order_number' => $this->order->order_number,
                'tracking_number' => $this->order->shipping_resi,
                'courier' => $this->order->shipping_courier,
                'customer_name' => $this->order->customer_name,
                'tracking_url' => URL::temporarySignedRoute(
                    'track.show',
                    now()->addDays($ttlDays),
                    ['order_number' => $this->order->order_number],
                ),
            ],
        );
    }
}
