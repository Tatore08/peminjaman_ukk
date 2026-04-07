<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengembalian', function (Blueprint $table) {
            $table->increments('pengembalian_id');
            $table->unsignedInteger('peminjaman_id');
            $table->date('tanggal_kembali_aktual');
            $table->string('kondisi_alat', 20);
            $table->integer('keterlambatan_hari')->default(0);
            $table->decimal('tarif_denda_per_hari', 10, 2)->nullable();
            $table->decimal('denda_keterlambatan', 10, 2)->default(0);
            $table->string('status_denda', 20)->default('belum_lunas')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->string('status_pengembalian', 20)->default('pending');
            $table->text('catatan')->nullable();
            $table->integer('persen_kerusakan')->default(0);
            $table->decimal('denda_kerusakan', 12, 2)->default(0);

            $table->foreign('peminjaman_id')
                  ->references('peminjaman_id')
                  ->on('peminjaman')
                  ->onDelete('restrict');
        });

        DB::statement("ALTER TABLE pengembalian ADD CONSTRAINT pengembalian_status_check 
            CHECK (status_pengembalian IN ('pending', 'approved'))");

        DB::statement("ALTER TABLE pengembalian ADD CONSTRAINT pengembalian_kondisi_check 
            CHECK (kondisi_alat IN ('baik', 'rusak'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('pengembalian');
    }
};
