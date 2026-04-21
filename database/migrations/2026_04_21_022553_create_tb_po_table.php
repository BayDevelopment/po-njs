<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_po', function (Blueprint $table) {

            $table->id('id_po');

            $table->unsignedInteger('id_pengajuan');

            $table->tinyInteger('versi_po')
                ->default(1)
                ->comment('1=original, 2=revisi, dst');

            $table->string('nomor_po', 50)
                ->unique()
                ->nullable();

            $table->string('dokumen_po')->nullable();

            $table->decimal('harga_penawaran', 15, 2)->nullable();
            $table->decimal('harga_deal', 15, 2)->nullable();

            $table->date('tanggal_po')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();

            $table->enum('status_po', [
                'draft',
                'diajukan',
                'revisi',
                'final',
                'ditolak'
            ])->default('draft');

            $table->enum('status_kerjasama', [
                'negosiasi',
                'deal',
                'batal',
                'proses',
                'selesai'
            ])->default('negosiasi');

            $table->string('dokumen_invoice')->nullable();
            $table->string('bukti_pembayaran')->nullable();

            $table->date('tanggal_pembayaran')->nullable();

            $table->enum('status_pembayaran', [
                'unpaid',
                'partial',
                'paid'
            ])->default('unpaid');

            $table->text('keterangan')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // ✅ cukup SATU FK
            $table->foreign('id_pengajuan')
                ->references('id_pengajuan')
                ->on('tb_pengajuan_kerjasama')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_po');
    }
};
