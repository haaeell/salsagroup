<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $fillable = [
        'gambar',
        'kode',
        'nama',
        'kategori_id',
        'harga',
        'harga_modal',
        'stok',
        'batas_stok_minimum',
        'satuan'
    ];
    protected $table = 'barang';

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function barangMasuk()
    {
        return $this->hasMany(BarangMasuk::class);
    }

    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class);
    }
}
