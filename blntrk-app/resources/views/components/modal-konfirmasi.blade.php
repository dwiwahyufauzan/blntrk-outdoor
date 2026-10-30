<div x-data="modalKonfirmasiHandler()"
     @tampilkan-konfirmasi.window="buka($event.detail)"
     @keydown.escape.window="tutup()"
     x-cloak>

    <!-- Backdrop Blur -->
    <div x-show="terbuka"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/60 backdrop-blur-xs z-[9999] flex items-center justify-center p-4">

        <!-- Card Dialog Box (Foresta Style) -->
        <div x-show="terbuka"
             @click.away="tutup()"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
             class="bg-white rounded-[28px] max-w-md w-full p-6 sm:p-7 shadow-2xl border border-stone-200 text-left relative overflow-hidden">

            <!-- Decorative Top Accent Line -->
            <div class="absolute top-0 inset-x-0 h-1.5"
                 :class="{
                     'bg-rose-500': tipe === 'danger',
                     'bg-amber-500': tipe === 'warning',
                     'bg-[#5edd5b]': tipe === 'primary' || tipe === 'success'
                 }"></div>

            <div class="flex items-start gap-4 mb-4">
                <!-- Icon Badge Container -->
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 shadow-xs"
                     :class="{
                         'bg-rose-100 text-rose-600 border border-rose-200': tipe === 'danger',
                         'bg-amber-100 text-amber-700 border border-amber-200': tipe === 'warning',
                         'bg-[#5edd5b]/20 text-[#0e1b15] border border-[#5edd5b]/40': tipe === 'primary' || tipe === 'success'
                     }">

                    <!-- Danger / Hapus Icon -->
                    <template x-if="tipe === 'danger'">
                        <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </template>

                    <!-- Warning Icon -->
                    <template x-if="tipe === 'warning'">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </template>

                    <!-- Primary / Success Icon -->
                    <template x-if="tipe === 'primary' || tipe === 'success'">
                        <svg class="w-6 h-6 text-[#0e1b15]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </template>
                </div>

                <!-- Text Headings -->
                <div class="flex-1 min-w-0 pt-0.5">
                    <h3 class="text-base sm:text-lg font-black text-stone-900 font-urbanist tracking-tight leading-snug"
                        x-text="judul"></h3>
                    <p class="text-xs text-stone-500 mt-1.5 leading-relaxed"
                       x-text="pesan"></p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 pt-4 border-t border-stone-100 flex items-center justify-end gap-2.5">
                <button type="button" @click="tutup()"
                        class="px-4 py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold transition cursor-pointer">
                    Batal
                </button>

                <button type="button" @click="konfirmasi()"
                        class="px-5 py-2.5 rounded-xl text-xs font-extrabold shadow-sm transition inline-flex items-center gap-1.5 cursor-pointer font-urbanist"
                        :class="{
                            'bg-rose-600 hover:bg-rose-700 text-white': tipe === 'danger',
                            'bg-amber-500 hover:bg-amber-600 text-white': tipe === 'warning',
                            'bg-[#5edd5b] hover:bg-[#4ed04b] text-black': tipe === 'primary' || tipe === 'success'
                        }">
                    <span x-text="tombolTeks"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function modalKonfirmasiHandler() {
        return {
            terbuka: false,
            tipe: 'danger',
            judul: 'Konfirmasi Tindakan',
            pesan: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
            tombolTeks: 'Ya, Lanjutkan',
            callback: null,

            buka(data) {
                this.tipe = data.tipe || 'danger';
                this.judul = data.judul || (this.tipe === 'danger' ? 'Konfirmasi Hapus Data' : 'Konfirmasi Tindakan');
                this.pesan = data.pesan || 'Apakah Anda yakin ingin melanjutkan? Data yang diproses tidak dapat dibatalkan.';
                this.tombolTeks = data.tombolTeks || (this.tipe === 'danger' ? 'Ya, Hapus' : 'Ya, Lanjutkan');
                this.callback = data.callback || null;
                this.terbuka = true;
            },

            tutup() {
                this.terbuka = false;
                this.callback = null;
            },

            konfirmasi() {
                if (typeof this.callback === 'function') {
                    this.callback();
                }
                this.tutup();
            }
        };
    }

    // Global Helper Function
    window.konfirmasiAksi = function (options) {
        window.dispatchEvent(new CustomEvent('tampilkan-konfirmasi', { detail: options }));
    };

    // Global Form Interceptor for forms with data-konfirmasi
    document.addEventListener('DOMContentLoaded', function () {
        document.body.addEventListener('submit', function (e) {
            const form = e.target;
            if (form && form.hasAttribute('data-konfirmasi') && !form.dataset.sudahKonfirmasi) {
                e.preventDefault();
                const tipe = form.getAttribute('data-konfirmasi') || 'danger';
                const judul = form.getAttribute('data-judul') || (tipe === 'danger' ? 'Konfirmasi Hapus Data' : 'Konfirmasi Tindakan');
                const pesan = form.getAttribute('data-pesan') || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
                const tombolTeks = form.getAttribute('data-tombol') || (tipe === 'danger' ? 'Ya, Hapus' : 'Ya, Lanjutkan');

                window.konfirmasiAksi({
                    tipe: tipe,
                    judul: judul,
                    pesan: pesan,
                    tombolTeks: tombolTeks,
                    callback: function () {
                        form.dataset.sudahKonfirmasi = 'true';
                        form.submit();
                    }
                });
            }
        });
    });
</script>

