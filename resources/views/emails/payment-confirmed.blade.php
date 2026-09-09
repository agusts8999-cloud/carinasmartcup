<!DOCTYPE html>
<html lang="id">
<head><meta charset="utf-8"><title>Pembayaran dikonfirmasi</title></head>
<body style="font-family: sans-serif; color: #111; line-height: 1.5;">
    <h1>Pembayaran dikonfirmasi</h1>
    <p>Halo {{ $order->customer_name }}, pembayaran untuk pesanan <strong>{{ $order->number }}</strong> sudah kami terima.</p>
    <p>Pesanan Anda sedang diproses gudang.</p>
    <p><a href="{{ url('/pesanan/'.$order->number) }}">Lacak status pesanan</a></p>
</body>
</html>
