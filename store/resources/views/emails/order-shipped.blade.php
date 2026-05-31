<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Dikirim</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #1e293b; max-width: 600px; margin: 0 auto; padding: 24px;">
    <h1 style="color: #4f46e5;">Pesanan Dikirim</h1>
    <p>Halo <strong>{{ $customer_name }}</strong>,</p>
    <p>Pesanan Anda dengan nomor <strong>{{ $order_number }}</strong> telah dikirim.</p>
    <p>
        Nomor resi: <strong>{{ $tracking_number }}</strong><br>
        Kurir: <strong>{{ $courier }}</strong>
    </p>
    <p>
        <a href="{{ $tracking_url }}" style="display: inline-block; background-color: #4f46e5; color: #fff; text-decoration: none; padding: 12px 24px; border-radius: 8px; margin-top: 8px;">Lacak Pesanan</a>
    </p>
    <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 24px 0;">
    <p style="color: #64748b; font-size: 14px;">Terima kasih sudah berbelanja di Firman Pratama.</p>
</body>
</html>
