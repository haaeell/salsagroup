<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->integer('harga_beli')->after('jumlah')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->dropColumn('harga_beli');
        });
    }
};
