<?php

namespace App;

use App\Traits\LogsActivityTrait;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use LogsActivityTrait;

    protected $table = 'product';
    protected $guarded = [];

    public function getMatchedFormula(){
        // Hapus angka + satuan di akhir product name
        $cleanName = preg_replace('/\b\d+\s*(ml|l|mg|g|kg)\b/i', '', $this->name);
        $cleanName = trim($cleanName);

        // Cek langsung di table Formula
        return \App\Formula::where('nama_formula', 'LIKE', "%{$cleanName}%")->first();
    }
    public function customerPrices()
    {
        return $this->hasMany(CustomerPrice::class, 'product_id');
    }

    public function getPriceForUser($userId)
    {
        $specificPrice = $this->customerPrices()->where('user_id', $userId)->first();
        if ($specificPrice) {
            return $specificPrice->price;
        }
        return $this->price; // Default price
    }

    public function produkjadi()
    {
        return $this->hasOne(SerahTerimaBarang::class, 'product_id');
    }

    function brand() {
        return $this->belongsTo('App\Brand', 'brand_id');
    }

    public function stockIn()
    {
        return $this->hasMany(StockInProduct::class, 'product_id');
    }

    public function stockOut()
    {
        return $this->hasMany(StockOutProduct::class, 'product_id');
    }

    public function getTotalStok(): float
    {
        $totalIn  = $this->stockIn()->sum('quantity');
        $totalOut = $this->stockOut()->sum('quantity');
        return max(0, $totalIn - $totalOut);
    }

    public function getCalculatedBatches()
    {
        // Ambil SEMUA stock_in_product (primer + koreksi opname), urut expired_date FIFO
        $allStockIn = \App\StockInProduct::where('product_id', $this->id)
            ->with(['stockOuts', 'produksi'])
            ->orderBy('expired_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Pisahkan: koreksi opname (punya parent_stock_in_product_id) vs primer
        $correctionsByParent = $allStockIn
            ->filter(fn($s) => !is_null($s->parent_stock_in_product_id))
            ->groupBy('parent_stock_in_product_id');

        // Record primer = semua yang BUKAN koreksi opname
        $stockIn = $allStockIn
            ->filter(fn($s) => is_null($s->parent_stock_in_product_id))
            ->values();

        // Gabungkan quantity + stockOuts dari koreksi ke parent masing-masing
        foreach ($stockIn as $primary) {
            if ($correctionsByParent->has($primary->id)) {
                $corrections = $correctionsByParent[$primary->id];

                // Quantity koreksi ditambahkan ke saldo batch induk
                $primary->quantity += $corrections->sum('quantity');

                // stockOut yang mengacu ke record koreksi diikutkan ke parent
                $correctionIds = $corrections->pluck('id')->toArray();
                $correctionOuts = \App\StockOutProduct::whereIn('stock_in_product_id', $correctionIds)->get();
                $primary->stockOuts = $primary->stockOuts->merge($correctionOuts);
            }
        }

        // validIds = semua ID (primer + koreksi) → stock_out ke koreksi tidak masuk unassigned
        $allStockInIds = $allStockIn->pluck('id')->toArray();

        $unassignedStockOut = \App\StockOutProduct::where('product_id', $this->id)
            ->where(function ($query) use ($allStockInIds) {
                $query->whereNull('stock_in_product_id')
                      ->orWhereNotIn('stock_in_product_id', $allStockInIds);
            })->sum('quantity');

        // Hitung sisa_stok_calculated per batch primer (FIFO)
        $batches = $stockIn->map(function ($item) use (&$unassignedStockOut) {
            $assignedOut = $item->stockOuts->sum('quantity');
            $available   = $item->quantity - $assignedOut;

            if ($available < 0) {
                $unassignedStockOut += abs($available);
                $available = 0;
            }

            if ($unassignedStockOut > 0) {
                if ($unassignedStockOut >= $available) {
                    $unassignedStockOut -= $available;
                    $item->sisa_stok_calculated = 0;
                } else {
                    $item->sisa_stok_calculated = $available - $unassignedStockOut;
                    $unassignedStockOut = 0;
                }
            } else {
                $item->sisa_stok_calculated = $available;
            }

            return $item;
        });

        return [
            'stockIn'                => $batches,
            'unassignedOutRemaining' => $unassignedStockOut,
        ];
    }
}

