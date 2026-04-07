<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
            $table->string('kondisi', 20)->default('baik');
            $table->string('lokasi', 100)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->string('status', 20)->default('tersedia');
            $table->decimal('harga_beli', 12, 2)->default(0);

            $table->foreign('kategori_id')
                  ->references('kategori_id')
                  ->on('kategori')
                  ->onDelete('restrict');

            $table->index('kategori_id', 'idx_alat_kategori');
        });

        // Tambah constraint check untuk status
        DB::statement("ALTER TABLE alat ADD CONSTRAINT alat_status_check 
            CHECK (status IN ('tersedia', 'dipinjam', 'rusak', 'pending'))");

        // Tambah constraint check untuk kondisi
        DB::statement("ALTER TABLE alat ADD CONSTRAINT alat_kondisi_check 
            CHECK (kondisi IN ('baik', 'rusak', 'hilang'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('alat');
    }
};
