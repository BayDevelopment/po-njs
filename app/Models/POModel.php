<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class POModel extends Model
{
    protected $connection = 'njs'; // karena ambil dari DB CI

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
}
