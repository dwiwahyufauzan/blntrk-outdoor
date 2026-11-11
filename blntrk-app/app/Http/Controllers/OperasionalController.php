<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class OperasionalController extends Controller
{
    /**
     * Pemeriksaan kesehatan yang ikut memastikan database dapat dihubungi (berbeda dengan /up yang hanya cek aplikasi).
     */
    public function health(): JsonResponse
    {
        try {
            DB::select('select 1');
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['status' => 'error', 'database' => 'tidak terhubung'], 503);
        }

        return response()->json(['status' => 'ok', 'database' => 'terhubung']);
    }

    /**
     * Pemicu penjadwalan dari layanan cron eksternal. Hosting yang tidur saat sepi (Render free) membuat
     * loop scheduler di dalam kontainer ikut berhenti, sehingga status terlambat/no-show perlu dipicu dari luar.
     * Dilindungi token melalui header `Authorization: Bearer <token>` atau `X-Cron-Token`. Token tidak diterima lewat
     * query string karena URL tercatat di log akses server dan proxy.
     */
    public function jadwal(Request $request): JsonResponse
    {
        $token = (string) config('services.cron.token');
        $diberikan = (string) ($request->bearerToken() ?? $request->header('X-Cron-Token', ''));

        abort_if($token === '' || ! hash_equals($token, $diberikan), 404);

        Artisan::call('schedule:run');

        return response()->json(['status' => 'ok', 'waktu' => now()->toIso8601String()]);
    }
}
