<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockOpnameExport implements FromView, ShouldAutoSize, WithStyles, WithTitle
{
    protected $data;
    protected $type;
    protected $tanggal;

    public function __construct($data, $type, $tanggal)
    {
        $this->data = $data;
        $this->type = $type;
        $this->tanggal = $tanggal;
    }

    public function view(): View
    {
        if ($this->type == 'Produk Jadi') {
            return view('stock-opname.reports.produk-jadi', [
                'data' => $this->data,
                'tanggal' => $this->tanggal
            ]);
        } elseif ($this->type == 'Bahan Baku') {
            return view('stock-opname.reports.bahan-baku', [
                'data' => $this->data,
                'tanggal' => $this->tanggal
            ]);
        } else {
            return view('stock-opname.reports.bahan-kemas', [
                'data' => $this->data,
                'tanggal' => $this->tanggal
            ]);
        }
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
            2    => ['font' => ['bold' => true]],
            3    => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Stock Opname';
    }
}
