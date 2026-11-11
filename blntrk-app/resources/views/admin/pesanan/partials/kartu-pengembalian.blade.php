                <!-- Kartu Hasil Pengembalian & Denda (Jika Sudah Dikembalikan) -->
                @if($pesanan->pengembalian)
                    @php $kembali = $pesanan->pengembalian; @endphp
                    <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs overflow-hidden">
                        <div class="p-5 border-b border-stone-100 flex items-center justify-between">
                            <h2 class="text-sm font-extrabold text-[#14201a] bl-d flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Hasil Pemeriksaan Pengembalian Alat
                            </h2>
                            <span class="text-xs text-stone-500 font-mono">{{ $kembali->dikembalikan_pada->format('d/m/Y H:i') }} WIB</span>
                        </div>

                        @if(abs($kembali->dikembalikan_pada->diffInMinutes($kembali->created_at)) > \App\Services\PengembalianService::TOLERANSI_WAKTU_MANUAL_MENIT)
                            <div class="px-5 py-2.5 border-b border-amber-200 bg-amber-50 text-[11px] text-amber-900">
                                Waktu pengembalian diisi manual oleh petugas. Waktu sistem saat data dicatat: <strong>{{ $kembali->created_at->format('d/m/Y H:i') }} WIB</strong>.
                            </div>
                        @endif

                        <div class="p-5 border-b border-stone-100 bg-stone-50/40 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-stone-400 block mb-0.5">Diterima Oleh</span>
                                <div class="font-bold text-stone-800">{{ $kembali->penerima?->nama ?? 'Admin' }}</div>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-stone-400 block mb-0.5">Keterlambatan</span>
                                <div class="font-bold {{ $kembali->hari_terlambat > 0 ? 'text-rose-700' : 'text-emerald-700' }}">
                                    {{ $kembali->hari_terlambat > 0 ? $kembali->hari_terlambat . ' Hari' : 'Tepat Waktu' }}
                                </div>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-stone-400 block mb-0.5">Catatan Kasir</span>
                                <div class="text-stone-600 italic">{{ $kembali->catatan ?: '-' }}</div>
                            </div>
                        </div>

                        <!-- Kondisi Alat Tiap Item -->
                        <div class="p-5">
                            <span class="text-[10px] uppercase font-bold text-stone-400 block mb-2 tracking-wider">Kondisi Alat Saat Diterima</span>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-stone-50 text-stone-400 uppercase text-[10px] font-bold border-b border-stone-200">
                                        <tr>
                                            <th class="py-2 px-3">Nama Alat</th>
                                            <th class="py-2 px-2 text-center text-emerald-700">Baik</th>
                                            <th class="py-2 px-2 text-center text-amber-700">R. Ringan</th>
                                            <th class="py-2 px-2 text-center text-orange-700">R. Berat</th>
                                            <th class="py-2 px-2 text-center text-rose-700">Hilang</th>
                                            <th class="py-2 px-3">Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-stone-100">
                                        @foreach($kembali->detail as $kd)
                                            <tr>
                                                <td class="py-2.5 px-3 font-bold text-stone-800">{{ $kd->pesananDetail->alat->nama }}</td>
                                                <td class="py-2.5 px-2 text-center font-bold text-emerald-700">{{ $kd->jml_baik }}</td>
                                                <td class="py-2.5 px-2 text-center font-bold text-amber-700">{{ $kd->jml_rusak_ringan }}</td>
                                                <td class="py-2.5 px-2 text-center font-bold text-orange-700">{{ $kd->jml_rusak_berat }}</td>
                                                <td class="py-2.5 px-2 text-center font-bold text-rose-700">{{ $kd->jml_hilang }}</td>
                                                <td class="py-2.5 px-3 text-stone-500 text-[11px]">{{ $kd->catatan ?: '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
