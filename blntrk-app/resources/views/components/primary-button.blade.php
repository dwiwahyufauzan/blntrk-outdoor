<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2.5 bg-[#5edd5b] border border-transparent rounded-xl font-extrabold text-xs text-black uppercase tracking-wider hover:bg-[#4ed04b] focus:bg-[#4ed04b] active:bg-[#3ec03b] focus:outline-none focus:ring-2 focus:ring-[#5edd5b] focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm hover:shadow-md cursor-pointer disabled:opacity-50']) }}>
    {{ $slot }}
</button>
