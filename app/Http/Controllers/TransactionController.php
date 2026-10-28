<?php

namespace App\Http\Controllers;

use App\Manufacturers;
use App\Inventory;
use App\StockIn;
use App\LogWebsite;
use App\StockOut;
use App\MaterialProduksi;
use App\SuplierMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class TransactionController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    function index() {
        $stockIn  = StockIn::all();

        $inventory = Inventory::all();
        return view('transaction.stock-in', compact('stockIn', 'inventory'));
    }
    
    function getSupliers($id) {
        $suplier = SuplierMaterial::where('inventory_id', $id)->get();
        return response()->json($suplier);
    } 
    
    public function out() {
        $inventory = Inventory::all();
        $permintaan = MaterialProduksi::where('user_id', auth()->id())
            ->where('produksi_id', null)
            ->with('inventory')
            ->get()
            ->groupBy(function ($item) {
                return $item->keterangan . '--' . $item->created_at->format('dmyHi');
            });

        return view('transaction.stock-out', compact('permintaan', 'inventory'));
    }

    public function deleteIn($id)
    {
        $dataStockIn = StockIn::find($id);

        if (!$dataStockIn) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $dataStockIn->delete();

        $dataLog = [
            'action' => 'Hapus Transaksi Stok Masuk',
            'keterangan' => 'Data Transaksi Stok Masuk id: '.$id . ' ' .$dataStockIn->inventory->name. ' berhasil dihapus',
            'users_id' => auth()->id(),
        ];

        LogWebsite::create($dataLog);

        return redirect()->back()->with('success', 'Data berhasil dihapus.');
    }

    public function deleteOut($id)
    {
        $dataStockOut = StockOut::find($id);

        if (!$dataStockOut) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $dataStockOut->delete();

        $dataLog = [
            'action' => 'Hapus Transaksi Stok Keluar',
            'keterangan' => 'Data Transaksi Stok Keluar id: '.$id . ' ' .$dataStockOut->inventory->name. ' berhasil dihapus',
            'users_id' => auth()->id(),
        ];

        LogWebsite::create($dataLog);

        return redirect()->back()->with('success', 'Data berhasil dihapus.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'inventory_id'       => 'required|exists:inventory,id',
            'vendor'             => 'required|string|max:500',
            'stok'               => 'required|numeric|min:0.001',
            'tanggal_masuk'      => 'required|date',
            'tanggal_ed'         => 'nullable|date|after_or_equal:tanggal_masuk',
            'nama_penyerah'      => 'nullable|string|max:255',
            'nomor_batch'        => 'nullable|string|max:255',
            'nomor_sj'           => 'nullable|string|max:255',
            'keterangan'         => 'nullable|string|max:1000',
            'suplier_material_id'=> 'nullable|exists:suplier_material,id',
        ]);

        $inventory = Inventory::findOrFail($request->inventory_id);

        StockIn::create([
            'inventory_id'       => $request->inventory_id,
            'user_id'            => auth()->id(),          // ambil dari session, bukan form hidden
            'vendor'             => $request->vendor,
            'nama_penyerah'      => $request->nama_penyerah,
            'nomor_batch'        => $inventory->type === 'Bahan Baku' ? $request->nomor_batch : null,
            'nomor_sj'           => $request->nomor_sj,
            'stok'               => $request->stok,
            'tanggal_masuk'      => $request->tanggal_masuk,
            'tanggal_ed'         => $request->tanggal_ed,
            'keterangan'         => $request->keterangan,
            'suplier_material_id'=> $request->suplier_material_id,
            'kategori'           => 'incoming_material',
            'parent_stock_in_id' => null,                  // selalu null untuk stock-in manual
        ]);

        return redirect()->back()->with('success', 'Transaksi sudah dibuat');
    }
    

    public function requestStoreOut(Request $request)
    {
        // 1. Validasi input (Penting untuk data array)
        $request->validate([
            'inventory_id'   => 'required|array|min:1',
            'inventory_id.*' => 'required', // pastikan setiap id ada
            'jumlah.*'       => 'required|numeric|min:1',
            'keterangan'     => 'required|string',
        ]);

        DB::transaction(function () use ($request) {
            $inventoryIds = $request->inventory_id;
            $jumlahs      = $request->jumlah;

            // 3. Looping berdasarkan index array
            foreach ($inventoryIds as $index => $id) {
                MaterialProduksi::create([
                    'user_id'        => $request->user_id,
                    'keterangan'     => $request->keterangan,
                    'inventory_id'   => $id,
                    'jumlah'         => $jumlahs[$index],
                    'persetujuan'    => 'pending',
                ]);
            }
        });

        return redirect()->back()->with('success', 'Semua transaksi stok keluar berhasil dibuat');
    }
}