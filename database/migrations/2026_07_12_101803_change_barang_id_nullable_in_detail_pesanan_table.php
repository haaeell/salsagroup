<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $fk = $this->foreignKeyName();

        DB::statement("ALTER TABLE detail_pesanan DROP FOREIGN KEY `{$fk}`");
        DB::statement('ALTER TABLE detail_pesanan MODIFY barang_id BIGINT UNSIGNED NULL');
        DB::statement("ALTER TABLE detail_pesanan ADD CONSTRAINT `{$fk}` FOREIGN KEY (barang_id) REFERENCES barang(id) ON DELETE SET NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $fk = $this->foreignKeyName();

        DB::statement("ALTER TABLE detail_pesanan DROP FOREIGN KEY `{$fk}`");
        DB::statement('ALTER TABLE detail_pesanan MODIFY barang_id BIGINT UNSIGNED NOT NULL');
        DB::statement("ALTER TABLE detail_pesanan ADD CONSTRAINT `{$fk}` FOREIGN KEY (barang_id) REFERENCES barang(id) ON DELETE CASCADE");
    }

    private function foreignKeyName(): string
    {
        $database = DB::getDatabaseName();

        return DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', 'detail_pesanan')
            ->where('COLUMN_NAME', 'barang_id')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->value('CONSTRAINT_NAME');
    }
};
