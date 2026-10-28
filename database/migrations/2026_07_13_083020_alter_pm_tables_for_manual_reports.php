<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'pm_pengambilan_sampel',
            'pm_inspeksi_penerimaan',
            'pm_pengujian',
            'pm_laporan_inspeksi'
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) {
            });
            
            try {
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE $table DROP FOREIGN KEY {$table}_stock_in_id_foreign");
            } catch (\Exception $e) {
                // Ignore if it's already dropped
            }

            Schema::table($table, function (Blueprint $t) {
                $t->unsignedBigInteger('stock_in_id')->nullable()->change();
                $t->unsignedBigInteger('pm_manual_report_id')->nullable()->after('stock_in_id');
                
                $t->foreign('stock_in_id')->references('id')->on('stock_in')->onDelete('cascade');
                $t->foreign('pm_manual_report_id')->references('id')->on('pm_manual_reports')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'pm_pengambilan_sampel',
            'pm_inspeksi_penerimaan',
            'pm_pengujian',
            'pm_laporan_inspeksi'
        ];

        foreach ($tables as $table) {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE $table DROP FOREIGN KEY {$table}_stock_in_id_foreign");
            Schema::table($table, function (Blueprint $t) {
                $t->dropForeign(['pm_manual_report_id']);
                $t->dropColumn('pm_manual_report_id');
                // Make stock_in_id required again
                $t->unsignedBigInteger('stock_in_id')->nullable(false)->change();
                $t->foreign('stock_in_id')->references('id')->on('stock_in')->onDelete('cascade');
            });
        }
    }
};
