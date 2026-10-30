<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class PublikLayout extends Component
{
    public function __construct(
        public ?string $title = null
    ) {}

    public function render(): View
    {
        $cart = session()->get('keranjang_sewa', []);
        $cartCount = (int) array_sum($cart);

        return view('layouts.publik', [
            'cartCount' => $cartCount,
        ]);
    }
}
