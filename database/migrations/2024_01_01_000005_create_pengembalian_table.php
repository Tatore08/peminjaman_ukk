<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengembalian', function (Blueprint $table) {
            $table->increments('pengembalian_id');
            $table->unsignedInteger('peminjaman_id');
            $table->date('tanggal_kembali_aktual');
            $table->enum('kondisi_alat', ['baik', 'rusak', 'hilang']);
            $table->integer('keterlambatan_hari')->default(0);
            $table->decimal('tarif_denda_per_hari', 10, 2)->nullable();
            $table->decimal('total_denda', 10, 2)->default(0);
            $table->enum('status_denda', ['lunas', 'belum_lunas'])->default('belum_lunas');
            $table->enum('status_pengembalian', ['pending', 'approved'])->default('pending');
            $table->text('keterangan')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('peminjaman_id')
                  ->references('peminjaman_id')
                  ->on('peminjaman')
                  ->onDelete('restrict');

            $table->index('peminjaman_id', 'idx_pengembalian_peminjaman');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengembalian');
    }
};
