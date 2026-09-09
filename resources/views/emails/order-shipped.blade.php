<!DOCTYPE html>
<html lang="id">
<head><meta charset="utf-8"><title>Pesanan dikirim</title></head>
<body style="font-family: sans-serif; color: #111; line-height: 1.5;">
    <h1>Pesanan dikirim</h1>
    <p>Halo {{ $order->customer_name }}, pesanan <strong>{{ $order->number }}</strong> telah dikirim.</p>
    @if($shipment->tracking_number)
        <p>Resi: <strong>{{ $shipment->tracking_number }}</strong>
            @if($shipment->courier_name) ({{ $shipment->courier_name }}) @endif
        </p>
    @endif
    @if($shipment->tracking_url)
        <p><a href="{{ $shipment->tracking_url }}">Lacak pengiriman</a></p>
    @endif
    <p><a href="{{ url('/pesanan/'.$order->number) }}">Detail pesanan</a></p>
</body>
</html>
