<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema;\n\nclass AddHargaBeliAndModifyPengembalian extends Migration
{
    public function up()
    {
        Schema::table('alat', function ($table) {
            $table->decimal('harga_beli', 10, 2)->nullable();
        });
\n        Schema::table('pengembalian', function ($table) {
            $table->renameColumn('total_denda', 'denda_keterlambatan');
            $table->decimal('persen_kerusakan', 5, 2)->nullable();
            $table->decimal('denda_kerusakan', 10, 2)->nullable();
        });
    }
\n    public function down()
    {
        Schema::table('alat', function ($table) {
            $table->dropColumn('harga_beli');
        });
\n        Schema::table('pengembalian', function ($table) {
            $table->renameColumn('denda_keterlambatan', 'total_denda');
            $table->dropColumn(['persen_kerusakan', 'denda_kerusakan']);
        });
    }
}
