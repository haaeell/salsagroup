<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $fillable = [
        'gambar',
        'kode',
        'nama',
        'deskripsi',
        'kategori_id',
        'harga',
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

    public static function generateKode(): string
    {
        $prefix = 'BRG';

        $lastNumber = self::where('kode', 'like', $prefix . '-%')
            ->get()
            ->map(fn ($barang) => (int) substr($barang->kode, strlen($prefix) + 1))
            ->max();

        $next = ($lastNumber ?? 0) + 1;

        do {
            $kode = $prefix . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
            $next++;
        } while (self::where('kode', $kode)->exists());

        return $kode;
    }
}
