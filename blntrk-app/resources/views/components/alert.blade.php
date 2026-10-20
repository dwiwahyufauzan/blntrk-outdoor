@if (session('sukses') || session('status'))
    <div class="mb-5 p-4 rounded-2xl bg-emerald-50/90 border border-emerald-200 text-emerald-900 flex items-center justify-between text-xs sm:text-sm shadow-xs" role="alert">
        <div class="flex items-center space-x-2.5">
            <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs flex-shrink-0">✓</span>
            <span class="font-medium">{{ session('sukses') ?? session('status') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold p-1">
            <span class="text-lg leading-none">&times;</span>
        </button>
    </div>
@endif

@if (session('error') || session('gagal'))
    <div class="mb-5 p-4 rounded-2xl bg-rose-50/90 border border-rose-200 text-rose-900 flex items-center justify-between text-xs sm:text-sm shadow-xs" role="alert">
        <div class="flex items-center space-x-2.5">
            <span class="w-6 h-6 rounded-full bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-xs flex-shrink-0">✕</span>
            <span class="font-medium">{{ session('error') ?? session('gagal') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900 font-bold p-1">
            <span class="text-lg leading-none">&times;</span>
        </button>
    </div>
@endif

@if ($errors->any())
    <div class="mb-5 p-4 rounded-2xl bg-amber-50/90 border border-amber-200 text-amber-950 text-xs sm:text-sm shadow-xs" role="alert">
        <div class="flex items-center space-x-2.5 font-bold mb-1.5 text-amber-900">
            <span class="w-6 h-6 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-xs flex-shrink-0">!</span>
            <span>Periksa kembali formulir input Anda:</span>
        </div>
        <ul class="list-disc list-inside text-xs space-y-1 ml-7 text-amber-800 font-medium">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

