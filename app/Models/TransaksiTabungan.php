<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiTabungan extends Model
{
    protected $table = 'transaksi_tabungan';
    protected $fillable = ['tabungan_id', 'pegawai_id', 'jenis_transaksi_id', 'jumlah', 'keterangan', 'tanggal_transaksi'];

    public function tabungan()
    {
        return $this->belongsTo(TabunganSiswa::class, 'tabungan_id');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function jenisTransaksi()
    {
        return $this->belongsTo(JenisTransaksiTabungan::class, 'jenis_transaksi_id');
    }
}

