<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alat', function (Blueprint $table) {
            $table->increments('alat_id');
            $table->unsignedInteger('kategori_id');
            $table->string('nama_alat', 100);
            $table->text('deskripsi')->nullable();
            $table->string('kode_alat', 50)->unique()->nullable();
            $table->enum('kondisi', ['baik', 'rusak', 'hilang'])->default('baik');
            $table->string('lokasi', 100)->nullable();
            $table->enum('status', ['tersedia', 'dipinjam', 'rusak'])->default('tersedia');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('kategori_id')
                  ->references('kategori_id')
                  ->on('kategori')
                  ->onDelete('restrict');

            $table->index('kategori_id', 'idx_alat_kategori');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alat');
    }
};
