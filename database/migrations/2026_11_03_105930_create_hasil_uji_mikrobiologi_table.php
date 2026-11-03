<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tabel ini menyimpan hasil uji antimikroba berdasarkan form KIG-QC-F-09-02-01.
     * Setiap record dapat terhubung ke satu sesi produksi (nullable untuk entri manual).
     * Kolom pengujian mencatat: jumlah awal, kondisi clean & dirty (CFU/mL + Log),
     * dan persentase penurunan untuk masing-masing kondisi.
     */
    public function up(): void
    {
        Schema::create('hasil_uji_mikrobiologi', function (Blueprint $table) {
            $table->id();

            // ─── Relasi ke Produksi (nullable untuk laporan manual) ──────────────────
            $table->unsignedBigInteger('produksi_id')->nullable();
            $table->foreign('produksi_id')
                  ->references('id')->on('produksi')
                  ->onDelete('set null');

            // ─── Identitas Laporan ────────────────────────────────────────────────────
            $table->date('tanggal');
            $table->string('no_batch')->nullable();             // diisi otomatis dari produksi atau manual
            $table->string('sampel_uji');                       // e.g. "Candida albicans"
            $table->string('no_dokumen')->default('KIG-QC-F-09-02-01');

            // Jenis Mikroba yang diuji (E. Coli / S. Aureus / C. albicans / lainnya)
            $table->string('mikroba_uji');

            // Waktu kontak (detik/menit) — form menunjukkan "30 Detik"
            $table->string('waktu_kontak')->default('30 Detik');

            // ─── Jumlah awal mikroba pada suspensi ───────────────────────────────────
            $table->decimal('awal_cfu_ml', 12, 2)->nullable();   // CFU/mL
            $table->decimal('awal_log', 8, 4)->nullable();        // Log₁₀

            // ─── Jumlah mikroba hidup – Clean Condition ──────────────────────────────
            $table->decimal('clean_cfu_ml', 12, 2)->nullable();
            $table->decimal('clean_log', 8, 4)->nullable();

            // ─── Jumlah mikroba hidup – Dirty Condition ──────────────────────────────
            $table->decimal('dirty_cfu_ml', 12, 2)->nullable();
            $table->decimal('dirty_log', 8, 4)->nullable();

            // ─── Persentase penurunan jumlah mikroba ─────────────────────────────────
            $table->decimal('persen_clean', 8, 4)->nullable();   // % Clean condition
            $table->decimal('persen_dirty', 8, 4)->nullable();   // % Dirty condition

            // ─── Keterangan / Catatan tambahan ───────────────────────────────────────
            $table->text('keterangan')->nullable();

            // ─── Petugas ─────────────────────────────────────────────────────────────
            $table->string('petugas')->nullable();

            // Flag: manual = tidak terhubung ke produksi
            $table->boolean('is_manual')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_uji_mikrobiologi');
    }
};
