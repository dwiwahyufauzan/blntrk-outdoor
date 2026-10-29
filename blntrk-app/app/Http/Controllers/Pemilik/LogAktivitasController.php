<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogAktivitasController extends Controller
{
    /**
     * Menampilkan riwayat jejak audit log aktivitas sistem (read-only).
     */
    public function index(Request $request): View
    {
        $dariInput = $request->query('dari');
        $sampaiInput = $request->query('sampai');
        $userId = $request->query('user_id');
        $modul = $request->query('modul');
        $q = $request->query('q');

        $query = LogAktivitas::with('user.role')
            ->latest('created_at');

        if ($dariInput) {
            $query->whereDate('created_at', '>=', Carbon::parse($dariInput)->toDateString());
        }

        if ($sampaiInput) {
            $query->whereDate('created_at', '<=', Carbon::parse($sampaiInput)->toDateString());
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($modul) {
            $query->where('modul', $modul);
        }

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('aksi', 'like', "%{$q}%")
                    ->orWhere('deskripsi', 'like', "%{$q}%")
                    ->orWhere('ip_address', 'like', "%{$q}%");
            });
        }

        $logs = $query->paginate(25)->withQueryString();

        $daftarUser = User::orderBy('nama')->get(['id', 'nama', 'email', 'role_id']);
        $daftarModul = LogAktivitas::select('modul')
            ->distinct()
            ->whereNotNull('modul')
            ->orderBy('modul')
            ->pluck('modul');

        return view('pemilik.log-aktivitas.index', compact(
            'logs',
            'daftarUser',
            'daftarModul',
            'dariInput',
            'sampaiInput',
            'userId',
            'modul',
            'q'
        ));
    }
}
