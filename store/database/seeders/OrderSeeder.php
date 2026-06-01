<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // B1: kelas sekarang dari Course model
        $kelasReguler = Course::where('slug', 'kelas-amc-reguler')->first();
        $bukuKeajaiban = Product::where('slug', '10-keajaiban-pikiran')->first();
        $bukuKitabSugesti = Product::where('slug', 'kitab-101-kalimat-sugesti-ajaib')->first();
        $bukuFormula = Product::where('slug', 'formula-amc-firman-pratama')->first();
        $bukuTelepathy = Product::where('slug', 'alpha-telepathy')->first();

        if (! $kelasReguler || ! $bukuKeajaiban) {
            // Data belum ter-seed. Skip.
            return;
        }

        // Sample 1 — pending (belum upload bukti)
        $this->createOrder([
            'order_number' => $this->generateOrderNumber('20260516-PND'),
            'customer_name' => 'Andi Pratama',
            'phone' => '081234567001',
            'email' => 'andi@example.com',
            'address' => 'Jl. Melati 12, Jakarta Selatan',
            'status' => 'pending',
            'created_at' => Carbon::now()->subDays(2),
            'items' => [[$bukuKeajaiban, 1]],
            'payments' => [],
        ]);

        // Sample 2 — partial_paid (DP cicilan kelas, verified)
        // B1: item kelas pakai Course model
        $this->createOrder([
            'order_number' => $this->generateOrderNumber('20260517-PRT'),
            'customer_name' => 'Sari Lestari',
            'phone' => '081234567002',
            'email' => 'sari@example.com',
            'address' => 'Jl. Kenanga 7, Bandung',
            'status' => 'partial_paid',
            'ref_code' => 'AFF-001',
            'created_at' => Carbon::now()->subDays(5),
            'items' => [[$kelasReguler, 1]],
            'payments' => [
                ['amount' => 1350000, 'method' => 'transfer', 'status' => 'verified',
                    'paid_at' => Carbon::now()->subDays(5), 'verified_at' => Carbon::now()->subDays(4)],
            ],
        ]);

        // Sample 3 — paid (lunas, sudah verified, siap kirim untuk buku fisik)
        $this->createOrder([
            'order_number' => $this->generateOrderNumber('20260518-PAID'),
            'customer_name' => 'Budi Santoso',
            'phone' => '081234567003',
            'email' => 'budi@example.com',
            'address' => 'Jl. Mawar 5, Surabaya',
            'status' => 'paid',
            'created_at' => Carbon::now()->subDays(3),
            'items' => [
                [$bukuFormula, 1],
                [$bukuKitabSugesti ?? $bukuKeajaiban, 1],
            ],
            'payments' => [
                ['amount' => 295000, 'method' => 'transfer', 'status' => 'verified',
                    'paid_at' => Carbon::now()->subDays(3), 'verified_at' => Carbon::now()->subDays(2)],
            ],
        ]);

        // Sample 4 — shipped (sudah dikirim, ada resi di status flow)
        $this->createOrder([
            'order_number' => $this->generateOrderNumber('20260514-SHP'),
            'customer_name' => 'Dewi Anggraini',
            'phone' => '081234567004',
            'email' => 'dewi@example.com',
            'address' => 'Jl. Anggrek 21, Yogyakarta',
            'status' => 'shipped',
            'ref_code' => 'AFF-002',
            'created_at' => Carbon::now()->subDays(7),
            'items' => [[$bukuTelepathy ?? $bukuKeajaiban, 2]],
            'payments' => [
                ['amount' => 350000, 'method' => 'transfer', 'status' => 'verified',
                    'paid_at' => Carbon::now()->subDays(7), 'verified_at' => Carbon::now()->subDays(6)],
            ],
        ]);

        // Sample 5 — completed (delivered, lifecycle penuh)
        $this->createOrder([
            'order_number' => $this->generateOrderNumber('20260510-CMP'),
            'customer_name' => 'Hadi Wirawan',
            'phone' => '081234567005',
            'email' => 'hadi@example.com',
            'address' => 'Jl. Cendana 33, Malang',
            'status' => 'completed',
            'created_at' => Carbon::now()->subDays(10),
            'items' => [[$bukuKeajaiban, 1]],
            'payments' => [
                ['amount' => 150000, 'method' => 'transfer', 'status' => 'verified',
                    'paid_at' => Carbon::now()->subDays(10), 'verified_at' => Carbon::now()->subDays(9)],
            ],
        ]);
    }

    /**
     * Create order with items and payments.
     * B1: detects Course vs Product to set correct FK (course_id or product_id).
     *
     * @param  array{order_number: string, customer_name: string, phone: string, email?: string, address: string, status: string, ref_code?: string, created_at?: Carbon, items: array<int, array{0: Model, 1: int}>, payments: array}  $data
     */
    private function createOrder(array $data): Order
    {
        $items = $data['items'];
        $payments = $data['payments'];
        unset($data['items'], $data['payments']);

        $total = 0;
        foreach ($items as [$model, $qty]) {
            $total += (float) $model->price * $qty;
        }

        $order = Order::create([
            'order_number' => $data['order_number'],
            'customer_name' => $data['customer_name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'address' => $data['address'],
            'total' => $total,
            'status' => $data['status'],
            'ref_code' => $data['ref_code'] ?? null,
            'created_at' => $data['created_at'] ?? now(),
            'updated_at' => $data['created_at'] ?? now(),
        ]);

        foreach ($items as [$model, $qty]) {
            $itemData = [
                'order_id' => $order->id,
                'qty' => $qty,
                'unit_price' => $model->price,
                'subtotal' => (float) $model->price * $qty,
            ];

            if ($model instanceof Course) {
                $itemData['course_id'] = $model->id;
            } else {
                $itemData['product_id'] = $model->id;
            }

            OrderItem::create($itemData);
        }

        foreach ($payments as $payment) {
            OrderPayment::create(array_merge(['order_id' => $order->id], $payment));
        }

        return $order;
    }

    private function generateOrderNumber(string $suffix): string
    {
        return 'MFP-'.$suffix.'-'.strtoupper(Str::random(6));
    }
}
