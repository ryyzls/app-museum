<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. INDEXING (Mempercepat Query)
        Schema::table('transactions', function (Blueprint $table) {
            $table->index(['payment_status', 'visit_date'], 'idx_trans_status_date');
        });
        Schema::table('tickets', function (Blueprint $table) {
            $table->index('exhibition_id', 'idx_tickets_exhibition');
        });

        // 2. MEMBUAT TABEL LOG UNTUK TRIGGER
        if (!Schema::hasTable('transaction_logs')) {
            Schema::create('transaction_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('transaction_id')->constrained('transactions')->cascadeOnDelete();
                $table->string('old_status', 50)->nullable();
                $table->string('new_status', 50)->nullable();
                $table->timestamp('changed_at')->useCurrent();
            });
        }

        // 3. TRIGGER (Mencatat history perubahan status tiket otomatis)
        DB::unprepared("
            DROP TRIGGER IF EXISTS trg_after_transaction_status_update;
            CREATE TRIGGER trg_after_transaction_status_update
            AFTER UPDATE ON transactions
            FOR EACH ROW
            BEGIN
                IF OLD.payment_status <> NEW.payment_status THEN
                    INSERT INTO transaction_logs (transaction_id, old_status, new_status)
                    VALUES (NEW.id, OLD.payment_status, NEW.payment_status);
                END IF;
            END
        ");

        // 4. SQL VIEW (Tabel Virtual untuk Rekap Pameran)
        DB::unprepared("
            DROP VIEW IF EXISTS vw_exhibition_performance;
            CREATE VIEW vw_exhibition_performance AS
            SELECT 
                e.id AS exhibition_id,
                e.title AS exhibition_title,
                m.name AS museum_name,
                COUNT(t.id) AS total_orders,
                SUM(t.quantity) AS total_tickets_sold,
                SUM(t.total_price) AS total_revenue
            FROM exhibitions e
            JOIN museums m ON e.museum_id = m.id
            JOIN tickets tk ON e.id = tk.exhibition_id
            JOIN transactions t ON tk.id = t.ticket_id
            WHERE t.payment_status = 'paid'
            GROUP BY e.id, e.title, m.name;
        ");

        // 5. STORED PROCEDURE (Untuk Laporan Transaksi)
        DB::unprepared("
            DROP PROCEDURE IF EXISTS sp_generate_revenue_report;
            CREATE PROCEDURE sp_generate_revenue_report(IN p_start_date DATE, IN p_end_date DATE)
            BEGIN
                SELECT 
                    visit_date,
                    COUNT(id) as total_transactions,
                    SUM(quantity) as tickets_sold,
                    SUM(total_price) as daily_revenue
                FROM transactions
                WHERE payment_status = 'paid'
                  AND visit_date BETWEEN p_start_date AND p_end_date
                GROUP BY visit_date
                ORDER BY visit_date ASC;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_generate_revenue_report");
        DB::unprepared("DROP VIEW IF EXISTS vw_exhibition_performance");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_after_transaction_status_update");
        Schema::dropIfExists('transaction_logs');

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex('idx_tickets_exhibition');
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('idx_trans_status_date');
        });
    }
};