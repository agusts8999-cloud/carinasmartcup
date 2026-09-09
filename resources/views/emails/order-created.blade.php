<!DOCTYPE html>
<html lang="id">
<head><meta charset="utf-8"><title>Pesanan dibuat</title></head>
<body style="font-family: sans-serif; color: #111; line-height: 1.5;">
    <h1>Terima kasih, {{ $order->customer_name }}!</h1>
    <p>Pesanan <strong>{{ $order->number }}</strong> berhasil dibuat dan menunggu pembayaran.</p>
    <p>Total: <strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong></p>
    <p>Batas pembayaran: {{ optional($order->payment_due_at)->timezone(config('app.timezone'))->format('d M Y H:i') }}</p>
    <p>
        Transfer ke:<br>
        {{ config('carina.bank.name') }} — {{ config('carina.bank.account_number') }} a/n {{ config('carina.bank.account_name') }}
    </p>
    <p><a href="{{ url('/pesanan/'.$order->number) }}">Lihat invoice &amp; unggah bukti</a></p>
</body>
</html>
