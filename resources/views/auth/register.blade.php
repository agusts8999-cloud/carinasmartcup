<x-guest-layout>
    <h2 class="text-lg font-bold text-emerald-900 mb-6 text-center">Daftar Akun</h2>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" value="Nama Lengkap" />
            <x-text-input id="name" class="block mt-1 w-full border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="whatsapp" value="WhatsApp" />
            <x-text-input id="whatsapp" class="block mt-1 w-full border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500" type="tel" name="whatsapp" :value="old('whatsapp')" required placeholder="08xxxxxxxxxx" autocomplete="tel" />
            <x-input-error :messages="$errors->get('whatsapp')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Password" />
            <x-text-input id="password" class="block mt-1 w-full border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Konfirmasi Password" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <a class="text-sm text-emerald-700 hover:text-emerald-900 underline" href="{{ route('login') }}">
                Sudah punya akun?
            </a>

            <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition">
                Daftar
            </button>
        </div>
    </form>
</x-guest-layout>
