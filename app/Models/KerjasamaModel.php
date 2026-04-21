<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KerjasamaModel extends Model
{
    protected $connection = 'mysql_ci';

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

    // relasi 
    public function po()
    {
        return $this->hasMany(
            POModel::class,
            'id_pengajuan',
            'id_pengajuan'
        );
    }
}
