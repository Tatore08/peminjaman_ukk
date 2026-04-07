<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // varchar biasa, tinggal update constraint check-nya
        DB::statement("ALTER TABLE alat DROP CONSTRAINT IF EXISTS alat_status_check");
        DB::statement("ALTER TABLE alat ADD CONSTRAINT alat_status_check 
            CHECK (status IN ('tersedia', 'dipinjam', 'rusak', 'pending'))");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE alat DROP CONSTRAINT IF EXISTS alat_status_check");
        DB::statement("ALTER TABLE alat ADD CONSTRAINT alat_status_check 
            CHECK (status IN ('tersedia', 'dipinjam', 'rusak'))");
    }
};