<?php

namespace App\Models\Siswa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiSiswa extends Model
{
    use HasFactory;

    protected $table = 'nilai_siswa';
    protected $fillable = [
        'siswa_nis',
        'mapel_id',
        'kelas_id',
        'tahun_ajaran_id',
        'nilai',
        'jenis_nilai',
        'tanggal_ambil',
        'guru_id',      
        'deskripsi',    
    ];

    
    public function siswa()
    {
        return $this->belongsTo(\App\Models\Master\Siswa::class, 'siswa_nis', 'nis');
    }

    
    public function mapel()
    {
        return $this->belongsTo(\App\Models\Master\Mapel::class, 'mapel_id');
    }

    
    public function kelas()
    {
        return $this->belongsTo(\App\Models\Master\Kelas::class, 'kelas_id');
    }
 
    public function tahunAjaran()
    {
        return $this->belongsTo(\App\Models\Master\TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function guru()
    {
        return $this->belongsTo(\App\Models\Master\Pegawai::class, 'guru_id')
                    ->where('objectjenispegawaifk', 1);
    }
}
