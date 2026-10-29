<?php

namespace App\Exports;

use App\Produksi;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Contracts\View\View;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ProductionExport implements FromView, ShouldAutoSize, WithEvents
{
    protected $produksiList;

    public function __construct()
    {
        $this->produksiList = Produksi::with([
            'product',
            'bahanBaku',
            'bahanBaku.bahanBaku.inventory',
            'produkJadi',
        ])->orderBy('tanggal_produksi', 'desc')->get();
    }

    public function view(): View
    {
        return view('exports.export-production', [
            'produksiList' => $this->produksiList,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Landscape A4
                $sheet->getPageSetup()->setOrientation(
                    \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE
                );
                $sheet->getPageSetup()->setPaperSize(
                    \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4
                );
                $sheet->getPageSetup()->setFitToPage(true);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);

                $lastRow = $sheet->getHighestRow();
                $lastCol = $sheet->getHighestColumn();

                // Title row (row 1): merge + center + bold
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 13],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(20);

                // Header row (row 2): bold + center + border
                $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 9],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(36);

                // Data rows: border + wrap
                if ($lastRow > 2) {
                    $sheet->getStyle("A3:{$lastCol}{$lastRow}")->applyFromArray([
                        'font'      => ['size' => 9],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_TOP,
                            'wrapText' => true,
                        ],
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                        ],
                    ]);
                }

                // Column widths
                $sheet->getColumnDimension('A')->setWidth(28); // Nama Produk
                $sheet->getColumnDimension('B')->setWidth(18); // No Batch
                $sheet->getColumnDimension('C')->setWidth(20); // Kode Produk
                $sheet->getColumnDimension('D')->setWidth(14); // Qty Hasil
                $sheet->getColumnDimension('E')->setWidth(10); // Satuan
                $sheet->getColumnDimension('F')->setWidth(16); // Tgl Produksi
                $sheet->getColumnDimension('G')->setWidth(16); // Tgl Kadaluarsa
                $sheet->getColumnDimension('H')->setWidth(18); // Kapasitas Produksi
                $sheet->getColumnDimension('I')->setWidth(48); // Keterangan
            },
        ];
    }
}
