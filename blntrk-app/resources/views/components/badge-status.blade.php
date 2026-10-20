@props(['status'])

@php
    $statusEnum = $status instanceof \App\Enums\StatusPesanan 
        ? $status 
        : \App\Enums\StatusPesanan::tryFrom($status);

    $label = $statusEnum ? $statusEnum->label() : ucfirst($status);
    $warna = $statusEnum ? $statusEnum->warna() : 'secondary';

    $classes = match($warna) {
        'warning' => 'bg-amber-100 text-amber-800 border-amber-300',
        'info'    => 'bg-blue-100 text-blue-800 border-blue-300',
        'success' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
        'danger'  => 'bg-rose-100 text-rose-800 border-rose-300',
        'secondary' => 'bg-gray-100 text-gray-800 border-gray-300',
        'dark'    => 'bg-zinc-200 text-zinc-800 border-zinc-400',
        default   => 'bg-gray-100 text-gray-800 border-gray-200',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {$classes}"]) }}>
    {{ $label }}
</span>
