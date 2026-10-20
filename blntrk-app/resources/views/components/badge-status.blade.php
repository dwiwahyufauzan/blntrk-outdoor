@props(['status'])

@php
    $statusEnum = $status instanceof \App\Enums\StatusPesanan 
        ? $status 
        : \App\Enums\StatusPesanan::tryFrom($status);

    $label = $statusEnum ? $statusEnum->label() : ucfirst(str_replace('_', ' ', $status));
    $warna = $statusEnum ? $statusEnum->warna() : 'secondary';

    $classes = match($warna) {
        'warning' => 'bg-amber-50 text-amber-800 border-amber-200/80',
        'info'    => 'bg-blue-50 text-blue-800 border-blue-200/80',
        'success' => 'bg-[#1e3a2c]/10 text-[#1e3a2c] border-[#1e3a2c]/20 font-bold',
        'danger'  => 'bg-rose-50 text-rose-800 border-rose-200/80 font-bold',
        'secondary' => 'bg-stone-100 text-stone-700 border-stone-200',
        'dark'    => 'bg-[#0e1b15] text-[#f6c453] border-[#1e3a2c] font-bold',
        default   => 'bg-stone-100 text-stone-700 border-stone-200',
    };

    $dotColor = match($warna) {
        'warning' => 'bg-amber-500',
        'info'    => 'bg-blue-500',
        'success' => 'bg-emerald-600',
        'danger'  => 'bg-rose-600',
        'dark'    => 'bg-[#f6c453]',
        default   => 'bg-stone-400',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold border {$classes} shadow-2xs"]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }}"></span>
    {{ $label }}
</span>
