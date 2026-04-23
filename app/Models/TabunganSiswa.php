<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TabunganSiswa extends Model
{
    protected $table = 'tabungan_siswa';
    protected $fillable = ['student_id', 'saldo'];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'nis');
    }

    public function transaksi()
    {
        return $this->hasMany(TransaksiTabungan::class, 'tabungan_id');
    }
}
