@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-stone-300 focus:border-[#1e3a2c] focus:ring-[#1e3a2c] rounded-xl shadow-xs text-sm text-[#14201a] placeholder:text-stone-400']) }}>
