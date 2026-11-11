{{-- Rincian kas keluar: dana yang dikembalikan kepada pelanggan atas pesanan dibatalkan. Dipakai laporan admin dan pemilik. --}}
@props(['data', 'linkPesanan' => false])

<div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs overflow-hidden mt-6">
    <div class="p-4 bg-stone-50/70 border-b border-stone-200/80 flex items-center justify-between">
        <h3 class="text-xs font-extrabold text-[#14201a] uppercase tracking-wider">3. Rincian Kas Keluar Refund Pesanan Dibatalkan</h3>
        <span class="text-xs font-bold text-rose-700">Subtotal: -Rp{{ number_format($data['total_refund'], 0, ',', '.') }}</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-stone-700">
            <thead class="bg-stone-50/50 text-stone-500 uppercase text-[10px] font-bold border-b border-stone-200 tracking-wider">
                <tr>
                    <th scope="col" class="py-2.5 px-4 text-center w-12">No</th>
                    <th scope="col" class="py-2.5 px-4">Waktu Refund</th>
                    <th scope="col" class="py-2.5 px-4">No. Resi</th>
                    <th scope="col" class="py-2.5 px-4">Pelanggan</th>
                    <th scope="col" class="py-2.5 px-4">Metode</th>
                    <th scope="col" class="py-2.5 px-4 text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($data['refund'] as $idx => $r)
                    <tr class="hover:bg-stone-50/60 transition">
                        <td class="py-2.5 px-4 text-center font-bold text-stone-400">{{ $idx + 1 }}</td>
                        <td class="py-2.5 px-4 font-mono text-stone-600">{{ $r->direfund_pada?->format('d/m/Y H:i') }}</td>
                        <td class="py-2.5 px-4 font-mono font-bold text-[#14201a]">
                            @if($linkPesanan)
                                <a href="{{ route('admin.pesanan.show', $r->pesanan_id) }}" class="hover:underline">{{ $r->pesanan?->no_resi ?? '-' }}</a>
                            @else
                                {{ $r->pesanan?->no_resi ?? '-' }}
                            @endif
                        </td>
                        <td class="py-2.5 px-4 font-bold text-stone-800">{{ $r->pesanan?->pelanggan?->nama_lengkap ?? '-' }}</td>
                        <td class="py-2.5 px-4">{{ $r->metode->label() }}</td>
                        <td class="py-2.5 px-4 text-right font-mono font-bold text-rose-700">-Rp{{ number_format($r->jumlah, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-stone-400 font-medium">Tidak ada refund pada periode ini</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
