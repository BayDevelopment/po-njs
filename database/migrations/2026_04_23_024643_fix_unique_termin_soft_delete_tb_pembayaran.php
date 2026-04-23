<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('mysql_ci')->table('tb_pembayaran', function (Blueprint $table) {
            // Hapus unique lama
            $table->dropUnique('tb_pembayaran_id_po_termin_unique');
        });

        // Buat unique baru yang ignore soft deleted
        // Caranya: pakai unique index yang include deleted_at IS NULL
        DB::connection('mysql_ci')->statement('
            CREATE UNIQUE INDEX tb_pembayaran_id_po_termin_active_unique
            ON tb_pembayaran (id_po, termin, deleted_at)
        ');
    }

    public function down(): void
    {
        DB::connection('mysql_ci')->statement('
            DROP INDEX tb_pembayaran_id_po_termin_active_unique ON tb_pembayaran
        ');

        Schema::connection('mysql_ci')->table('tb_pembayaran', function (Blueprint $table) {
            $table->unique(['id_po', 'termin']);
        });
    }
};
