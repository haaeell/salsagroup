<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->unsignedInteger('remaining_jumlah')->default(0)->after('jumlah');
        });

        DB::table('barang_masuk')->update([
            'remaining_jumlah' => DB::raw('jumlah'),
        ]);

        Schema::table('detail_pesanan', function (Blueprint $table) {
            $table->json('fifo_layers')->nullable()->after('harga_modal');
        });
    }

    public function down(): void
    {
        Schema::table('detail_pesanan', function (Blueprint $table) {
            $table->dropColumn('fifo_layers');
        });

        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->dropColumn('remaining_jumlah');
        });
    }
};
