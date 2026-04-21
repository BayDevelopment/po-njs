<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'bukti_pembayaran',
        'tanggal_pembayaran',
        'status_pembayaran',
        'keterangan',
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

    protected static function booted()
    {
        static::creating(function ($model) {

            $year = date('Y');

            // 🔹 ROMAWI (dibuat jelas)
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

            // 🔹 LOOP BIAR TIDAK DUPLIKAT
            do {
                $count = self::whereYear('created_at', $year)->count() + 1;

                $nomor = 'PO/'
                    . str_pad($count, 3, '0', STR_PAD_LEFT)
                    . '/' . $bulan
                    . '/' . $year;
            } while (self::where('nomor_po', $nomor)->exists());

            $model->nomor_po = $nomor;

            // 🔹 VERSI PO (SUDAH BENAR)
            $lastVersion = self::where('id_pengajuan', $model->id_pengajuan)
                ->max('versi_po');

            $model->versi_po = $lastVersion ? $lastVersion + 1 : 1;
        });
    }
}
