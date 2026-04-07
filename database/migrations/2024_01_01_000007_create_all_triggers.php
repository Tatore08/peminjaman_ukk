<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // =============================================
        // FUNCTION 1: update_updated_at_column
        // =============================================
        DB::unprepared("
            CREATE OR REPLACE FUNCTION public.update_updated_at_column()
            RETURNS TRIGGER AS \$\$
            BEGIN
                NEW.updated_at = CURRENT_TIMESTAMP;
                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql;
        ");

        // Trigger updated_at untuk semua tabel
        DB::unprepared("
            DROP TRIGGER IF EXISTS update_alat_updated_at ON alat;
            CREATE TRIGGER update_alat_updated_at
                BEFORE UPDATE ON alat
                FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();
        ");
        DB::unprepared("
            DROP TRIGGER IF EXISTS update_kategori_updated_at ON kategori;
            CREATE TRIGGER update_kategori_updated_at
                BEFORE UPDATE ON kategori
                FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();
        ");
        DB::unprepared("
            DROP TRIGGER IF EXISTS update_peminjaman_updated_at ON peminjaman;
            CREATE TRIGGER update_peminjaman_updated_at
                BEFORE UPDATE ON peminjaman
                FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();
        ");
        DB::unprepared("
            DROP TRIGGER IF EXISTS update_pengembalian_updated_at ON pengembalian;
            CREATE TRIGGER update_pengembalian_updated_at
                BEFORE UPDATE ON pengembalian
                FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();
        ");
        DB::unprepared("
            DROP TRIGGER IF EXISTS update_users_updated_at ON users;
            CREATE TRIGGER update_users_updated_at
                BEFORE UPDATE ON users
                FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();
        ");

        // =============================================
        // FUNCTION 2: generate_kode_alat
        // =============================================
        DB::unprepared("
            CREATE OR REPLACE FUNCTION public.generate_kode_alat()
            RETURNS TRIGGER AS \$\$
            DECLARE
                v_prefix VARCHAR(3);
                v_last_number INTEGER;
                v_new_number INTEGER;
                v_new_kode VARCHAR(50);
                v_kategori_nama VARCHAR(100);
            BEGIN
                -- Kalau kode_alat udah diisi manual, skip
                IF NEW.kode_alat IS NOT NULL AND NEW.kode_alat != '' THEN
                    RETURN NEW;
                END IF;

                -- Ambil nama kategori
                SELECT nama_kategori INTO v_kategori_nama
                FROM kategori
                WHERE kategori_id = NEW.kategori_id;

                -- Buat prefix dari 3 huruf pertama kategori (uppercase)
                v_prefix := UPPER(SUBSTRING(v_kategori_nama, 1, 3));

                -- Cari kode terakhir dengan prefix yang sama
                SELECT COALESCE(MAX(CAST(SUBSTRING(kode_alat FROM 5) AS INTEGER)), 0)
                INTO v_last_number
                FROM alat
                WHERE kode_alat LIKE v_prefix || '-%'
                  AND kode_alat IS NOT NULL;

                -- Increment
                v_new_number := v_last_number + 1;

                -- Generate kode baru: PREFIX-XXX (contoh: ELE-001)
                v_new_kode := v_prefix || '-' || LPAD(v_new_number::TEXT, 3, '0');

                -- Set ke NEW
                NEW.kode_alat := v_new_kode;

                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql;
        ");

        DB::unprepared("
            DROP TRIGGER IF EXISTS trigger_generate_kode_alat ON alat;
            CREATE TRIGGER trigger_generate_kode_alat
                BEFORE INSERT ON alat
                FOR EACH ROW
                EXECUTE FUNCTION public.generate_kode_alat();
        ");

        // =============================================
        // FUNCTION 3: fn_alat_on_peminjaman_insert
        // Saat peminjaman dibuat (pending) → alat jadi 'pending'
        // =============================================
        DB::unprepared("
            CREATE OR REPLACE FUNCTION public.fn_alat_on_peminjaman_insert()
            RETURNS TRIGGER AS \$\$
            BEGIN
                IF NEW.status = 'pending' THEN
                    UPDATE alat 
                    SET status = 'pending'
                    WHERE alat_id = NEW.alat_id;
                END IF;
                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql;
        ");

        DB::unprepared("
            DROP TRIGGER IF EXISTS tr_alat_on_peminjaman_insert ON peminjaman;
            CREATE TRIGGER tr_alat_on_peminjaman_insert
                AFTER INSERT ON peminjaman
                FOR EACH ROW
                EXECUTE FUNCTION public.fn_alat_on_peminjaman_insert();
        ");

        // =============================================
        // FUNCTION 4: fn_alat_on_peminjaman_approve
        // Saat peminjaman di-approve → alat jadi 'dipinjam'
        // =============================================
        DB::unprepared("
            CREATE OR REPLACE FUNCTION public.fn_alat_on_peminjaman_approve()
            RETURNS TRIGGER AS \$\$
            BEGIN
                IF NEW.status = 'approved' AND OLD.status = 'pending' THEN
                    UPDATE alat 
                    SET status = 'dipinjam'
                    WHERE alat_id = NEW.alat_id;
                END IF;
                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql;
        ");

        DB::unprepared("
            DROP TRIGGER IF EXISTS tr_alat_on_peminjaman_approve ON peminjaman;
            CREATE TRIGGER tr_alat_on_peminjaman_approve
                AFTER UPDATE ON peminjaman
                FOR EACH ROW
                EXECUTE FUNCTION public.fn_alat_on_peminjaman_approve();
        ");

        // =============================================
        // FUNCTION 5: fn_alat_on_peminjaman_reject
        // Saat peminjaman di-reject/cancel → alat balik 'tersedia'
        // =============================================
        DB::unprepared("
            CREATE OR REPLACE FUNCTION public.fn_alat_on_peminjaman_reject()
            RETURNS TRIGGER AS \$\$
            BEGIN
                IF NEW.status IN ('rejected', 'cancelled') AND OLD.status = 'pending' THEN
                    UPDATE alat 
                    SET status = 'tersedia'
                    WHERE alat_id = NEW.alat_id;
                END IF;
                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql;
        ");

        DB::unprepared("
            DROP TRIGGER IF EXISTS tr_alat_on_peminjaman_reject ON peminjaman;
            CREATE TRIGGER tr_alat_on_peminjaman_reject
                AFTER UPDATE ON peminjaman
                FOR EACH ROW
                EXECUTE FUNCTION public.fn_alat_on_peminjaman_reject();
        ");

        // =============================================
        // FUNCTION 6: process_pengembalian_approved
        // Saat pengembalian approved → alat balik 'tersedia'/'rusak'
        // + peminjaman jadi 'returned'
        // =============================================
        DB::unprepared("
            CREATE OR REPLACE FUNCTION public.process_pengembalian_approved()
            RETURNS TRIGGER AS \$\$
            DECLARE
                v_alat_id INTEGER;
            BEGIN
                IF NEW.status_pengembalian = 'approved' AND 
                   (TG_OP = 'INSERT' OR OLD.status_pengembalian != 'approved') THEN

                    -- Ambil alat_id dari peminjaman
                    SELECT alat_id INTO v_alat_id
                    FROM peminjaman
                    WHERE peminjaman_id = NEW.peminjaman_id;

                    -- Update status peminjaman jadi 'returned'
                    UPDATE peminjaman 
                    SET status = 'returned'
                    WHERE peminjaman_id = NEW.peminjaman_id;

                    -- Update status alat sesuai kondisi
                    IF NEW.kondisi_alat = 'baik' THEN
                        UPDATE alat SET status = 'tersedia' WHERE alat_id = v_alat_id;
                    ELSE
                        UPDATE alat SET status = 'rusak' WHERE alat_id = v_alat_id;
                    END IF;
                END IF;

                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql;
        ");

        DB::unprepared("
            DROP TRIGGER IF EXISTS trigger_process_pengembalian_approved ON pengembalian;
            CREATE TRIGGER trigger_process_pengembalian_approved
                AFTER INSERT OR UPDATE ON pengembalian
                FOR EACH ROW
                EXECUTE FUNCTION public.process_pengembalian_approved();
        ");

        // =============================================
        // FUNCTION 7: fn_check_kerusakan_70
        // Kerusakan >= 70% → alat jadi 'rusak' permanent
        // =============================================
        DB::unprepared("
            CREATE OR REPLACE FUNCTION public.fn_check_kerusakan_70()
            RETURNS TRIGGER AS \$\$
            BEGIN
                IF NEW.status_pengembalian = 'approved' AND NEW.persen_kerusakan >= 70 THEN
                    UPDATE alat 
                    SET status = 'rusak',
                        kondisi = 'rusak'
                    WHERE alat_id = (
                        SELECT alat_id FROM peminjaman 
                        WHERE peminjaman_id = NEW.peminjaman_id
                    );
                END IF;
                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql;
        ");

        DB::unprepared("
            DROP TRIGGER IF EXISTS tr_check_kerusakan_70 ON pengembalian;
            CREATE TRIGGER tr_check_kerusakan_70
                AFTER UPDATE ON pengembalian
                FOR EACH ROW
                EXECUTE FUNCTION public.fn_check_kerusakan_70();
        ");
    }

    public function down(): void
    {
        // Drop semua trigger
        DB::unprepared("DROP TRIGGER IF EXISTS update_alat_updated_at ON alat;");
        DB::unprepared("DROP TRIGGER IF EXISTS update_kategori_updated_at ON kategori;");
        DB::unprepared("DROP TRIGGER IF EXISTS update_peminjaman_updated_at ON peminjaman;");
        DB::unprepared("DROP TRIGGER IF EXISTS update_pengembalian_updated_at ON pengembalian;");
        DB::unprepared("DROP TRIGGER IF EXISTS update_users_updated_at ON users;");
        DB::unprepared("DROP TRIGGER IF EXISTS trigger_generate_kode_alat ON alat;");
        DB::unprepared("DROP TRIGGER IF EXISTS tr_alat_on_peminjaman_insert ON peminjaman;");
        DB::unprepared("DROP TRIGGER IF EXISTS tr_alat_on_peminjaman_approve ON peminjaman;");
        DB::unprepared("DROP TRIGGER IF EXISTS tr_alat_on_peminjaman_reject ON peminjaman;");
        DB::unprepared("DROP TRIGGER IF EXISTS trigger_process_pengembalian_approved ON pengembalian;");
        DB::unprepared("DROP TRIGGER IF EXISTS tr_check_kerusakan_70 ON pengembalian;");

        // Drop semua function
        DB::unprepared("DROP FUNCTION IF EXISTS public.update_updated_at_column();");
        DB::unprepared("DROP FUNCTION IF EXISTS public.generate_kode_alat();");
        DB::unprepared("DROP FUNCTION IF EXISTS public.fn_alat_on_peminjaman_insert();");
        DB::unprepared("DROP FUNCTION IF EXISTS public.fn_alat_on_peminjaman_approve();");
        DB::unprepared("DROP FUNCTION IF EXISTS public.fn_alat_on_peminjaman_reject();");
        DB::unprepared("DROP FUNCTION IF EXISTS public.process_pengembalian_approved();");
        DB::unprepared("DROP FUNCTION IF EXISTS public.fn_check_kerusakan_70();");
    }
};
