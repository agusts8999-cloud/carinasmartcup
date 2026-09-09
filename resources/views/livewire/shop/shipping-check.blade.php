<div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <h1 class="text-xl font-bold text-gray-900 mb-2">Cek Ongkir</h1>
    <p class="text-sm text-gray-500 mb-6">Estimasi biaya pengiriman berdasarkan lokasi dan berat paket.</p>

    <form wire:submit="calculate" class="bg-white rounded-xl border border-emerald-100 p-4 sm:p-6 space-y-4 mb-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi *</label>
            <input wire:model="province" type="text" placeholder="Contoh: Jawa Barat" class="w-full rounded-lg border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500">
            @error('province') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kota/Kabupaten</label>
            <input wire:model="city" type="text" placeholder="Contoh: Bandung" class="w-full rounded-lg border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Berat (kg) *</label>
                <input wire:model="weight_kg" type="number" step="0.1" min="0.1" class="w-full rounded-lg border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500">
                @error('weight_kg') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal (Rp)</label>
                <input wire:model="subtotal" type="number" min="0" class="w-full rounded-lg border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500">
            </div>
        </div>
        <button type="submit" class="w-full py-3 bg-emerald-600 text-white font-semibold rounded-full hover:bg-emerald-700">Hitung Ongkir</button>
    </form>

    @if($quotes !== [])
        <div class="space-y-3">
            <h2 class="font-semibold text-gray-900">Hasil Estimasi</h2>
            @foreach($quotes as $quote)
                <div class="bg-white rounded-xl border border-emerald-100 p-4 flex justify-between items-center">
                    <div>
                        <p class="font-medium text-gray-900">{{ $quote['name'] }}</p>
                        <p class="text-sm text-gray-500">{{ $quote['zone_name'] }} · {{ $quote['eta_days_min'] }}–{{ $quote['eta_days_max'] }} hari kerja</p>
                    </div>
                    <span class="font-bold text-emerald-700">Rp {{ number_format($quote['price'], 0, ',', '.') }}</span>
                </div>
            @endforeach
        </div>
    @endif
</div>
