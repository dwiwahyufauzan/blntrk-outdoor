<?php

use App\Services\WhatsAppService;

if (! function_exists('wa_link')) {
    /**
     * Menghasilkan tautan langsung WhatsApp dengan normalisasi nomor 628xxx.
     */
    function wa_link(?string $noHp, string $pesan): string
    {
        return WhatsAppService::link($noHp, $pesan);
    }
}
