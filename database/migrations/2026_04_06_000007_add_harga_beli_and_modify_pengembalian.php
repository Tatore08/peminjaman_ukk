<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alat', function (Blueprint $table) {
            $table->decimal('harga_beli', 12, 2)->default(0);
        });

        Schema::table('pengembalian', function (Blueprint $table) {
            $table->renameColumn('total_denda', 'denda_keterlambatan');
            $table->integer('persen_kerusakan')->default(0);
            $table->decimal('denda_kerusakan', 12, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('alat', function (Blueprint $table) {
            $table->dropColumn('harga_beli');
        });

        Schema::table('pengembalian', function (Blueprint $table) {
            $table->renameColumn('denda_keterlambatan', 'total_denda');
            $table->dropColumn(['persen_kerusakan', 'denda_kerusakan']);
        });
    }
};