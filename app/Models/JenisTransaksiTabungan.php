<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisTransaksiTabungan extends Model
{
    protected $table = 'jenis_transaksi_tabungan';
    protected $fillable = ['kode', 'nama'];

    public function transaksi()
    {
        return $this->hasMany(TransaksiTabungan::class, 'jenis_transaksi_id');
    }
}

