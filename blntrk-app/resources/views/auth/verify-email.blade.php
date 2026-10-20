<x-guest-layout>
    <div class="mb-4 text-xs text-stone-600 leading-relaxed">
        {{ __('Terima kasih telah mendaftar di BLNTRK Outdoor! Sebelum memulai, silakan verifikasi alamat email Anda melalui tautan yang baru saja kami kirimkan.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-bold text-xs text-emerald-700 bg-emerald-50 border border-emerald-200 p-3 rounded-xl">
            {{ __('Tautan verifikasi baru telah dikirim ke alamat email yang Anda daftarkan.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between pt-2">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Kirim Ulang Email Verifikasi') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="text-xs font-bold text-stone-500 hover:text-[#0e1b15] underline transition">
                {{ __('Keluar') }}
            </button>
        </form>
    </div>
</x-guest-layout>
