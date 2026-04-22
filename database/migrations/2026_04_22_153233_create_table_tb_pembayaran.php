<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mysql_ci')->create('tb_pembayaran', function (Blueprint $table) {

            $table->id('id_pembayaran');

            $table->unsignedBigInteger('id_po')->index();

            $table->tinyInteger('termin')
                ->comment('ke-1, ke-2, dst');

            // ✅ taruh langsung setelah termin, hapus ->after()
            $table->enum('jenis_termin', [
                'dp',
                'cicilan',
                'pelunasan',
            ])->default('pelunasan');

            $table->decimal('jumlah_bayar', 15, 2);

            $table->date('tanggal_pembayaran');

            $table->string('bukti_pembayaran')->nullable();

            $table->enum('metode', [
                'transfer',
                'tunai',
                'cek',
                'giro',
            ])->default('transfer');

            $table->text('keterangan')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['id_po', 'termin']);

            $table->foreign('id_po')
                ->references('id_po')
                ->on('tb_po')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::connection('mysql_ci')->dropIfExists('tb_pembayaran');
    }
};
