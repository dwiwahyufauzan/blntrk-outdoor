<nav x-data="{ open: false }" class="bg-[#0e1b15] border-b border-[#1e3a2c] text-stone-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-8">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('beranda') }}" class="flex items-center gap-2.5 group">
                        <img src="{{ asset('images/logo.png') }}" alt="BLNTRK Outdoor Logo" class="w-8 h-8 rounded-full object-cover shadow-xs ring-1 ring-white/15 group-hover:scale-105 transition">
                        <div class="leading-tight">
                            <span class="font-extrabold text-base tracking-tight text-white block bl-d">BLNTRK<span class="text-[#5edd5b]">.</span></span>
                            <span class="text-[9px] font-semibold text-stone-400 uppercase tracking-widest block -mt-1">Outdoor</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-6 sm:-my-px sm:flex">
                    <a href="{{ route('beranda') }}" class="inline-flex items-center px-1 pt-1 text-xs font-bold text-stone-300 hover:text-[#5edd5b] transition">
                        Beranda
                    </a>
                    @if(Auth::user()->role?->nama === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-1 pt-1 text-xs font-bold text-[#5edd5b] hover:text-[#4ed04b] transition">
                            Panel Admin
                        </a>
                    @elseif(Auth::user()->role?->nama === 'pemilik')
                        <a href="{{ route('pemilik.dashboard') }}" class="inline-flex items-center px-1 pt-1 text-xs font-bold text-[#5edd5b] hover:text-[#4ed04b] transition">
                            Dashboard Pemilik
                        </a>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-2 border border-[#1e3a2c] text-xs font-bold rounded-xl text-stone-200 bg-[#14201a] hover:bg-[#1e3a2c] hover:text-[#5edd5b] focus:outline-none transition">
                            <span>{{ Auth::user()->nama ?? Auth::user()->name }}</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded-md bg-[#1e3a2c] text-[#5edd5b] uppercase font-mono font-bold">
                                {{ Auth::user()->role?->nama ?? 'user' }}
                            </span>
                            <svg class="fill-current h-4 w-4 text-stone-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profil Saya') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Keluar') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-xl text-stone-400 hover:text-white hover:bg-[#1e3a2c] focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Backdrop Redup & Blur Saat Menu Mobile Terbuka (Bisa diklik di mana saja untuk menutup) -->
    <div x-show="open" x-cloak
         @click="open = false"
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 top-16 bg-black/70 backdrop-blur-md sm:hidden cursor-pointer z-30"
         aria-hidden="true"></div>

    <!-- Responsive Navigation Menu -->
    <div x-show="open" x-cloak
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="relative sm:hidden border-t border-[#1e3a2c] bg-[#14201a] shadow-2xl z-40">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <a href="{{ route('beranda') }}" class="block py-2 text-xs font-bold text-stone-300 hover:text-[#5edd5b]">
                Beranda
            </a>
            @if(Auth::user()->role?->nama === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="block py-2 text-xs font-bold text-[#5edd5b]">
                    Panel Admin
                </a>
            @elseif(Auth::user()->role?->nama === 'pemilik')
                <a href="{{ route('pemilik.dashboard') }}" class="block py-2 text-xs font-bold text-[#5edd5b]">
                    Dashboard Pemilik
                </a>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-3 border-t border-[#1e3a2c] px-4">
            <div class="font-bold text-sm text-white">{{ Auth::user()->nama ?? Auth::user()->name }}</div>
            <div class="text-xs text-stone-400 font-mono">{{ Auth::user()->email }}</div>

            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}" class="block py-1.5 text-xs font-semibold text-stone-300 hover:text-[#5edd5b]">
                    Profil Saya
                </a>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();"
                            class="block py-1.5 text-xs font-semibold text-rose-400 hover:text-rose-300">
                        Keluar
                    </a>
                </form>
            </div>
        </div>
    </div>
</nav>
