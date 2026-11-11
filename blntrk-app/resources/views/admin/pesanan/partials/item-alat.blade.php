                <!-- Tabel Item Alat -->
                <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs overflow-hidden">
                    <div class="p-5 border-b border-stone-100 flex items-center justify-between">
                        <h2 class="text-sm font-extrabold text-[#14201a] bl-d flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#5edd5b]"></span>
                            Perlengkapan Outdoor yang Disewa
                        </h2>
                        <span class="text-xs text-stone-500 font-bold">{{ $pesanan->detail->count() }} Jenis Alat ({{ $pesanan->detail->sum('jumlah') }} Unit)</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-stone-50/80 text-stone-500 uppercase text-[10px] font-bold border-b border-stone-200 tracking-wider">
                                <tr>
                                    <th class="py-3 px-5">Nama Alat</th>
                                    <th class="py-3 px-4 text-center">Jumlah</th>
                                    <th class="py-3 px-4 text-right">Tarif / Hari</th>
                                    <th class="py-3 px-5 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-100">
                                @foreach($pesanan->detail as $item)
                                    <tr>
                                        <td class="py-3.5 px-5">
                                            <div class="font-bold text-[#14201a] text-sm bl-d">{{ $item->alat->nama }}</div>
                                            <div class="text-[10px] font-mono text-stone-400">{{ $item->alat->kode_alat }} &bull; {{ $item->alat->kategori?->nama }}</div>
                                        </td>
                                        <td class="py-3.5 px-4 text-center font-bold text-stone-800">
                                            {{ $item->jumlah }} Unit
                                        </td>
                                        <td class="py-3.5 px-4 text-right font-mono text-stone-600">
                                            Rp{{ number_format($item->harga_per_hari, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3.5 px-5 text-right font-bold text-[#14201a] font-mono">
                                            Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-t-2 border-stone-200 bg-stone-50/50">
@if($pesanan->diskon_paket > 0)
                                <tr>
                                    <td colspan="3" class="py-2 px-5 text-right font-bold text-[#2f8f2c] uppercase text-[11px]">
                                        Diskon {{ $pesanan->paketBundling?->nama ?? 'Paket Bundling' }}{{ $pesanan->jumlah_paket > 1 ? ' x'.$pesanan->jumlah_paket : '' }}
                                    </td>
                                    <td class="py-2 px-5 text-right font-mono font-bold text-[#2f8f2c]">
                                        -Rp{{ number_format($pesanan->diskon_paket, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endif
                                                                <tr>
                                    <td colspan="3" class="py-3.5 px-5 text-right font-bold text-stone-500 uppercase text-[11px]">
                                        Total Biaya Sewa ({{ $pesanan->durasi_hari }} Hari)
                                    </td>
                                    <td class="py-3.5 px-5 text-right font-black text-base text-[#14201a] font-mono">
                                        Rp{{ number_format($pesanan->total_sewa, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($pesanan->catatan)
                        <div class="p-4 border-t border-stone-100 bg-stone-50/50 text-xs text-stone-600">
                            <strong class="text-stone-800">Catatan Pemesan:</strong> {{ $pesanan->catatan }}
                        </div>
                    @endif
                </div>
