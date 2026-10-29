@if (session('sukses') || session('status'))
    <div class="mb-5 p-4 rounded-2xl bg-emerald-50/95 border border-emerald-200 text-emerald-950 flex items-center justify-between text-xs sm:text-sm shadow-xs" role="alert">
        <div class="flex items-center space-x-3">
            <span class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
            </span>
            <span class="font-semibold">{{ session('sukses') ?? session('status') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-950 p-1 transition cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
@endif

@if (session('error') || session('gagal'))
    <div class="mb-5 p-4 rounded-2xl bg-rose-50/95 border border-rose-200 text-rose-950 flex items-center justify-between text-xs sm:text-sm shadow-xs" role="alert">
        <div class="flex items-center space-x-3">
            <span class="w-6 h-6 rounded-full bg-rose-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
            </span>
            <span class="font-semibold">{{ session('error') ?? session('gagal') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-950 p-1 transition cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
@endif

@if ($errors->any())
    <div class="mb-5 p-4 rounded-2xl bg-amber-50/95 border border-amber-200 text-amber-950 text-xs sm:text-sm shadow-xs" role="alert">
        <div class="flex items-center space-x-3 font-bold mb-1.5 text-amber-900">
            <span class="w-6 h-6 rounded-full bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </span>
            <span>Periksa kembali formulir input Anda:</span>
        </div>
        <ul class="list-disc list-inside text-xs space-y-1 ml-9 text-amber-800 font-medium">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

