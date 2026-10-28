<?php

namespace App;
use App\Traits\LogsActivityTrait;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use LogsActivityTrait;
    protected $table = 'inventory';
    protected $guarded = [];

    public function stockIn() {
        return $this->hasMany('App\StockIn', 'inventory_id');
    }

    public function stockOut() {
        return $this->hasMany('App\StockOut', 'inventory_id');
    }

    public function specifications() {
        return $this->hasMany('App\SpesifikasiUjiMaterial', 'inventory_id');
    }

    public function manufacturers() {
        return $this->belongsToMany('App\Manufacturers', 'spesifikasi_uji_material', 'inventory_id', 'manufacturer_id')->withPivot('parameter');
    }

    // baru
    public function stockOpnames() { 
        return $this->hasMany('App\StockOpname', 'inventory_id');
    }

    public function getCalculatedBatches()
    {
        // Ambil SEMUA stock_in (primer + koreksi opname), urut FIFO
        $allStockIn = \App\StockIn::where('inventory_id', $this->id)
            ->with('stockOuts')
            ->orderBy('tanggal_masuk', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Pisahkan: koreksi opname (punya parent_stock_in_id) vs primer
        $correctionsByParent = $allStockIn
            ->filter(fn($s) => !is_null($s->parent_stock_in_id))
            ->groupBy('parent_stock_in_id');

        // Record primer = semua yang BUKAN koreksi opname
        $stockIn = $allStockIn
            ->filter(fn($s) => is_null($s->parent_stock_in_id))
            ->values();

        // Gabungkan stok + stockOuts dari koreksi ke parent masing-masing
        foreach ($stockIn as $primary) {
            if ($correctionsByParent->has($primary->id)) {
                $corrections = $correctionsByParent[$primary->id];

                // Stok koreksi ditambahkan ke saldo batch induk
                $primary->stok += $corrections->sum('stok');

                // stockOut yang mengacu ke record koreksi diikutkan ke parent
                $correctionIds = $corrections->pluck('id')->toArray();
                $correctionOuts = \App\StockOut::whereIn('stock_in_id', $correctionIds)->get();
                $primary->stockOuts = $primary->stockOuts->merge($correctionOuts);
            }
        }

        // validIds = semua ID (primer + koreksi) → stock_out ke koreksi tidak masuk unassigned
        $allStockInIds = $allStockIn->pluck('id')->toArray();

        $unassignedStockOut = \App\StockOut::where('inventory_id', $this->id)
            ->where(function ($query) use ($allStockInIds) {
                $query->whereNull('stock_in_id')
                      ->orWhereNotIn('stock_in_id', $allStockInIds);
            })->sum('jumlah');

        // Stok awal manual: saldo historis sebelum sistem dicatat
        $stokAwal    = $this->stok ?? 0;
        $sisaStokAwal = $stokAwal;

        if ($unassignedStockOut > 0) {
            if ($unassignedStockOut >= $sisaStokAwal) {
                $unassignedStockOut -= $sisaStokAwal;
                $sisaStokAwal = 0;
            } else {
                $sisaStokAwal -= $unassignedStockOut;
                $unassignedStockOut = 0;
            }
        }

        // Hitung sisa_stok_calculated per batch primer (FIFO)
        foreach ($stockIn as $in) {
            $assignedOut = $in->stockOuts->sum('jumlah');
            $available   = $in->stok - $assignedOut;

            if ($available < 0) {
                $unassignedStockOut += abs($available);
                $available = 0;
            }

            if ($unassignedStockOut > 0) {
                if ($unassignedStockOut >= $available) {
                    $unassignedStockOut -= $available;
                    $in->sisa_stok_calculated = 0;
                } else {
                    $in->sisa_stok_calculated = $available - $unassignedStockOut;
                    $unassignedStockOut = 0;
                }
            } else {
                $in->sisa_stok_calculated = $available;
            }
        }

        return [
            'stockIn'      => $stockIn,
            'sisaStokAwal' => $sisaStokAwal,
        ];
    }

    /**
     * Hitung total stok berjalan secara dinamis (tidak bergantung field stok manual).
     * stok_manual + total_stock_in - total_stock_out
     */
    public function getTotalStok(): float
    {
        $stokAwal   = $this->stok ?? 0;
        $totalIn    = $this->stockIn()->sum('stok');
        $totalOut   = $this->stockOut()->sum('jumlah');
        return max(0, $stokAwal + $totalIn - $totalOut);
    }
}
