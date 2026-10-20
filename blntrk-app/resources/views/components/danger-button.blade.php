<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2.5 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white rounded-xl font-bold text-xs shadow-xs focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 transition']) }}>
    {{ $slot }}
</button>
