<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PembayaranModel extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_ci'; // WAJIB
    protected $table = 'tb_pembayaran';
    protected $primaryKey = 'id_pembayaran';

    protected $fillable = [
        'id_po',
        'termin',
        'jumlah_bayar',
        'tanggal_pembayaran',
        'bukti_pembayaran',
        'metode',
        'keterangan'
    ];

    public function po()
    {
        return $this->belongsTo(POModel::class, 'id_po', 'id_po');
    }
}
