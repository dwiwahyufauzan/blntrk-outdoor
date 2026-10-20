<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2.5 bg-[#0e1b15] border border-transparent rounded-xl font-bold text-xs text-[#f6c453] uppercase tracking-wider hover:bg-[#1e3a2c] focus:bg-[#1e3a2c] active:bg-[#0a140f] focus:outline-none focus:ring-2 focus:ring-[#f6c453] focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm cursor-pointer disabled:opacity-50']) }}>
    {{ $slot }}
</button>
