<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('barang_masuk')->delete();
        DB::table('barang')->update(['stok' => 0]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data reset is not reversible: original stok values and deleted
        // barang_masuk rows cannot be restored.
    }
};
