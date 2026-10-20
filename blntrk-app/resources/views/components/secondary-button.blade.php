<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-4 py-2.5 bg-white border border-stone-200/90 rounded-xl font-bold text-xs text-stone-700 shadow-xs hover:bg-stone-50 focus:outline-none focus:ring-2 focus:ring-[#1e3a2c] focus:ring-offset-2 disabled:opacity-25 transition']) }}>
    {{ $slot }}
</button>
