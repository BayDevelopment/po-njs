<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class POModel extends Model
{
    use SoftDeletes; // 🔥 WAJIB

    protected $connection = 'mysql_ci'; // karena ambil dari DB CI

    protected $table = 'tb_po';

    protected $primaryKey = 'id_po'; // sesuaikan dengan tabel

    protected $fillable = [
        'id_pengajuan',
        'versi_po',
        'nomor_po',
        'dokumen_po',
        'harga_penawaran',
        'harga_deal',
        'tanggal_po',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_po',
        'status_kerjasama',
        'dokumen_invoice',
        'status_pembayaran',
        'keterangan',
    ];

    // POModel.php
    protected $casts = [
        'dokumen_po'       => 'string', // ✅ pastikan string, bukan array
        'dokumen_invoice'  => 'string',
        'tanggal_po'       => 'date',
        'tanggal_mulai'    => 'date',
        'tanggal_selesai'  => 'date',
        'harga_penawaran'  => 'integer',
        'harga_deal'       => 'integer',
    ];

    // Relasi 
    public function kerjasama()
    {
        return $this->belongsTo(
            KerjasamaModel::class,
            'id_pengajuan',      // foreign key di tb_po
            'id_pengajuan'       // primary key di tb_pengajuan_kerjasama
        );
    }
    public function pembayaran()
    {
        return $this->hasMany(PembayaranModel::class, 'id_po');
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            // Log::info('Creating PO - dokumen_po value:', [
            //     'dokumen_po'      => $model->dokumen_po,
            //     'dokumen_invoice' => $model->dokumen_invoice,
            //     'bukti_pembayaran' => $model->bukti_pembayaran,
            //     'type_dokumen_po' => gettype($model->dokumen_po),
            // ]);

            $year = date('Y');

            $romawi = [
                1 => 'I',
                2 => 'II',
                3 => 'III',
                4 => 'IV',
                5 => 'V',
                6 => 'VI',
                7 => 'VII',
                8 => 'VIII',
                9 => 'IX',
                10 => 'X',
                11 => 'XI',
                12 => 'XII'
            ];

            $bulan = $romawi[(int) date('m')];

            // ✅ Ambil nomor urut tertinggi dari semua record (termasuk soft deleted)
            $lastNomor = self::withTrashed()
                ->whereYear('created_at', $year)
                ->max('nomor_po'); // contoh: "PO/003/IV/2026"

            // ✅ Ekstrak angka dari nomor terakhir, lalu +1
            $lastNumber = 0;
            if ($lastNomor) {
                preg_match('/PO\/(\d+)\//', $lastNomor, $matches);
                $lastNumber = isset($matches[1]) ? (int) $matches[1] : 0;
            }

            // ✅ Loop sampai dapat nomor yang benar-benar belum dipakai
            do {
                $lastNumber++;
                $nomor = 'PO/'
                    . str_pad($lastNumber, 3, '0', STR_PAD_LEFT)
                    . '/' . $bulan
                    . '/' . $year;
            } while (
                self::withTrashed()->where('nomor_po', $nomor)->exists()
            );

            $model->nomor_po = $nomor;

            // ✅ Versi PO (termasuk soft deleted biar konsisten)
            $lastVersion = self::withTrashed()
                ->where('id_pengajuan', $model->id_pengajuan)
                ->max('versi_po');

            $model->versi_po = $lastVersion ? $lastVersion + 1 : 1;
        });
    }
}
