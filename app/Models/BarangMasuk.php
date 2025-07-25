<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model
{
    protected $table = 'barang_masuk';
    protected $fillable = ['barang_id', 'jumlah', 'tanggal_masuk'];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
