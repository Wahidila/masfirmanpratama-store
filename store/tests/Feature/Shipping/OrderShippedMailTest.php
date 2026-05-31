<?php

namespace Tests\Feature\Shipping;

use App\Events\OrderShipped;
use App\Mail\OrderShippedMail;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderShippedMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_sends_email_when_order_shipped_with_tracking_and_email(): void
    {
        Mail::fake();

        $order = Order::factory()->create([
            'email' => 'customer@example.com',
            'shipping_courier' => 'JNE',
            'shipping_resi' => 'JNE1234567890',
            'shipped_email_sent_at' => null,
        ]);

        OrderShipped::dispatch($order);

        Mail::assertSent(OrderShippedMail::class, function (OrderShippedMail $mail) use ($order) {
            return $mail->order->id === $order->id
                && $mail->hasTo($order->email);
        });

        $order->refresh();

        $this->assertNotNull($order->shipped_email_sent_at);
    }

    public function test_idempotent_only_sends_one_email(): void
    {
        Mail::fake();

        $order = Order::factory()->create([
            'email' => 'customer@example.com',
            'shipping_courier' => 'JNE',
            'shipping_resi' => 'JNE1234567890',
            'shipped_email_sent_at' => null,
        ]);

        OrderShipped::dispatch($order);
        OrderShipped::dispatch($order);

        Mail::assertSent(OrderShippedMail::class, 1);
    }

    public function test_no_email_when_no_tracking_number(): void
    {
        Mail::fake();

        $order = Order::factory()->create([
            'email' => 'customer@example.com',
            'shipping_courier' => null,
            'shipping_resi' => null,
            'shipped_email_sent_at' => null,
        ]);

        OrderShipped::dispatch($order);

        Mail::assertNothingSent();
    }

    public function test_no_email_when_no_customer_email(): void
    {
        Mail::fake();

        $order = Order::factory()->create([
            'email' => null,
            'shipping_courier' => 'JNE',
            'shipping_resi' => 'JNE1234567890',
            'shipped_email_sent_at' => null,
        ]);

        OrderShipped::dispatch($order);

        Mail::assertNothingSent();
    }

    public function test_subject_contains_order_number(): void
    {
        Mail::fake();

        $order = Order::factory()->create([
            'email' => 'customer@example.com',
            'shipping_courier' => 'JNE',
            'shipping_resi' => 'JNE1234567890',
            'shipped_email_sent_at' => null,
        ]);

        OrderShipped::dispatch($order);

        Mail::assertSent(OrderShippedMail::class, function (OrderShippedMail $mail) use ($order) {
            return str_contains($mail->envelope()->subject, $order->order_number);
        });
    }
}
