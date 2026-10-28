<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOut extends Model
{
    use HasFactory;

    protected $table = 'stock_out';
    protected $guarded = [];

    protected $casts = [
        'tanggal_keluar' => 'date',
        'jumlah'         => 'decimal:3', // konsisten dengan decimal(10,3)
    ];

    // relasi
    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function stockIn()
    {
        return $this->belongsTo(StockIn::class, 'stock_in_id');
    }
    public function permintaanMaterial()
    {
        return $this->belongsTo(MaterialProduksi::class, 'permintaan_id');
    }

    // ── Scopes untuk filter berdasarkan kategori ─────────────────────────

    /**
     * Hanya stock out dari penyesuaian stock opname.
     * Berguna untuk laporan opname dan audit trail.
     */
    public function scopeOpname($query)
    {
        return $query->where('kategori', 'stock_opname');
    }

    /**
     * Hanya stock out dari permintaan produksi (bukan opname).
     */
    public function scopePermintaan($query)
    {
        return $query->where(function ($q) {
            $q->where('kategori', '!=', 'stock_opname')
              ->orWhereNull('kategori');
        });
    }

    /**
     * Filter berdasarkan kategori tertentu.
     * Contoh: StockOut::byKategori('stock_opname')->get()
     */
    public function scopeByKategori($query, string $kategori)
    {
        return $query->where('kategori', $kategori);
    }
}
