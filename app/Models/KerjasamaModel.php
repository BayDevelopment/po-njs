<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KerjasamaModel extends Model
{
    protected $table = 'tb_pengajuan_kerjasama';

    protected $primaryKey = 'id_pengajuan';

    protected $fillable = [
        'nama_perusahaan',
        'alamat_perusahaan',
        'penanggung_jawab',
        'jabatan',
        'telepon',
        'email',
        'ruang_lingkup_kerjasama',
        'dokumen_pendukung',
        'tanggal_pengajuan',
        'status_pengajuan',
        'alasan',
    ];
}
