@if (session('sukses') || session('status'))
    <div class="mb-4 p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-sm shadow-sm" role="alert">
        <div class="flex items-center space-x-2">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>{{ session('sukses') ?? session('status') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800">
            <span class="text-xl">&times;</span>
        </button>
    </div>
@endif

@if (session('error') || session('gagal'))
    <div class="mb-4 p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between text-sm shadow-sm" role="alert">
        <div class="flex items-center space-x-2">
            <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{{ session('error') ?? session('gagal') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800">
            <span class="text-xl">&times;</span>
        </button>
    </div>
@endif

@if ($errors->any())
    <div class="mb-4 p-4 rounded-lg bg-amber-50 border border-amber-200 text-amber-900 text-sm shadow-sm" role="alert">
        <div class="flex items-center space-x-2 font-semibold mb-1">
            <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <span>Mohon periksa kembali formulir Anda:</span>
        </div>
        <ul class="list-disc list-inside text-xs space-y-1 ml-5 text-amber-800">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
