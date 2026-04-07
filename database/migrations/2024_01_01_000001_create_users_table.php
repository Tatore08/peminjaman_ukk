<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Buat enum user_level
        DB::statement("DO $$ BEGIN
            CREATE TYPE user_level AS ENUM ('admin', 'petugas', 'peminjam');
        EXCEPTION WHEN duplicate_object THEN null; END $$;");

        Schema::create('users', function (Blueprint $table) {
            $table->increments('user_id');
            $table->string('username', 50)->unique();
            $table->string('password', 255);
            $table->string('level', 20)->default('peminjam');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        // Buat admin default
        DB::table('users')->insert([
            'username' => 'admin',
            'password' => bcrypt('admin123'),
            'level'    => 'admin',
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
