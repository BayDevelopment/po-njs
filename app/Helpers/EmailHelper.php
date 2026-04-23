<?php
// app/Helpers/EmailHelper.php

if (!function_exists('statusBadgePO')) {
    function statusBadgePO(string $status): string
    {
        return match ($status) {
            'draft'    => '🟡 Draft',
            'diajukan' => '🔵 Diajukan',
            'revisi'   => '🟠 Revisi',
            'final'    => '🟢 Final',
            'ditolak'  => '🔴 Ditolak',
            default    => ucfirst($status),
        };
    }
}

if (!function_exists('statusBadgeKerjasama')) {
    function statusBadgeKerjasama(string $status): string
    {
        return match ($status) {
            'negosiasi' => '🔵 Negosiasi',
            'deal'      => '🟢 Deal',
            'batal'     => '🔴 Batal',
            'proses'    => '🟡 Proses',
            'selesai'   => '✅ Selesai',
            default     => ucfirst($status),
        };
    }
}
