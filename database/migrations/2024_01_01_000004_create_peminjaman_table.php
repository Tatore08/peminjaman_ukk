<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->increments('peminjaman_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('alat_id');
            $table->date('tanggal_peminjaman');
            $table->date('tanggal_kembali_rencana');
            $table->text('tujuan_peminjaman')->nullable();
            $table->unsignedInteger('disetujui_oleh')->nullable();
            $table->timestamp('tanggal_disetujui')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->string('status', 20)->default('pending');
            $table->text('catatan')->nullable();

            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('restrict');

            $table->foreign('alat_id')
                  ->references('alat_id')
                  ->on('alat')
                  ->onDelete('restrict');

            $table->foreign('disetujui_oleh')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('set null');
        });

        DB::statement("ALTER TABLE peminjaman ADD CONSTRAINT peminjaman_status_check 
            CHECK (status IN ('pending', 'approved', 'rejected', 'returned'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
