<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use App\RekonsiliasiBahanKemas;
use App\PemeriksaanKebersihan;
use App\PenimbanganBahanBaku;
use App\MaterialProduksi;
use App\StockOut;
use App\QualityControlCheck;
use App\CheckTahapanProses;
use App\BagianKebersihan;
use App\Inventory;
use App\QualityControl;
use App\RuangProduksi;
use App\TahapanProses;
use App\Transaction;
use App\Produksi;
use App\RiwayatAlat;
use App\CheckSerahTerimaBarang;
use App\Invoice;
use App\Product;
use App\FormulaBahanBaku;
use App\FormulaBahanKemas;
use App\User;

class RuangProduksiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index() {
        $rooms = RuangProduksi::with('items')->get();

        return view('produksi.atur-ruangan', compact('rooms'));
    }

    public function proses($batch_number) {
        $produksiSaatini = Produksi::where('batch_number', $batch_number)->first();
        
        $produksiSebelumIni = Produksi::where('tanggal_produksi', $produksiSaatini->tanggal_produksi)
            ->where('batch_number', '<', $produksiSaatini->batch_number)
            ->orderBy('batch_number', 'desc')
            ->with('product')
            ->first();

        if (!$produksiSebelumIni) {
            $produksiSebelumIni = Produksi::where('tanggal_produksi', '<', $produksiSaatini->tanggal_produksi)
                ->orderBy('tanggal_produksi', 'desc')
                ->orderBy('batch_number', 'desc')
                ->with('product')
                ->first();
        }

        $riwayatAlat = RiwayatAlat::where('produksi_id', $produksiSaatini->id)->with('produksi', 'ruangProduksi')
            ->get()
            ->groupBy('ruang_produksi_id');

        
            // Pengambilan batch melalui permintaan
        $permintaanIds = MaterialProduksi::where('produksi_id', $produksiSaatini->id)->pluck('id');

        $stockOuts = StockOut::with('stockIn')
            ->whereIn('permintaan_id', $permintaanIds)
            ->get();

        $stockOutByInventory = $stockOuts->groupBy('inventory_id');

        $penimbanganBahanBaku = [];
        $rekonsiliasiBarang = [];
        if($produksiSaatini->is_custom_formula != 1){
            $penimbanganBahanBaku = FormulaBahanBaku::where(
                'formula_id',
                $produksiSaatini->formula_bahan_baku_id
            )
            ->get()
            ->map(function ($formula) use ($stockOutByInventory) {

                $batches = $stockOutByInventory[$formula->inventory_id] ?? collect();

                return [
                    'formula' => $formula,
                    'batches' => $batches->map(function ($so) {
                        return [
                            'stock_in_id' => $so->stock_in_id,
                            'nomor_batch' => optional($so->stockIn)->nomor_batch,
                            'jumlah'      => $so->jumlah,
                        ];
                    }),
                ];
            });
            $rekonsiliasiBarang = FormulaBahanKemas::where('formula_id', $produksiSaatini->formula_bahan_kemas_id)->get();
        }

        $rooms = RuangProduksi::with('items')->get();
        $pemeriksaanKebersihan = PemeriksaanKebersihan::with(['bagianKebersihan.ruangProduksi'])
        ->where('produksi_id', $produksiSaatini->id)
        ->get()
        ->groupBy('bagianKebersihan.ruang_produksi_id');
    
        $pemeriksaanPenimbang = PenimbanganBahanBaku::where('produksi_id', $produksiSaatini->id)
        ->get();

        $pemeriksaanProsesProduksi = CheckTahapanProses::with('tapahanProses')
        ->where('produksi_id', $produksiSaatini->id)
        ->get();

        $pemeriksaanQualityControl = QualityControlCheck::with('qualityControl')
        ->where('produksi_id', $produksiSaatini->id)
        ->get()->sortBy(fn($item) => $item->qualityControl->urutan ?? 999);
        
        $rekonsiliasiBahanKemas = RekonsiliasiBahanKemas::with('bahanKemas')
        ->where('produksi_id', $produksiSaatini->id)->get();

        $inventories = Inventory::all();

        $users = User::all();

        $quality = QualityControl::where('formula_id', $produksiSaatini->formula_bahan_baku_id)->get();
        $tahapanProses = TahapanProses::where('formula_id', $produksiSaatini->formula_bahan_baku_id)->get();

        return view('produksi.mulai-produksi', compact('rooms','produksiSebelumIni','inventories', 'users', 'riwayatAlat', 'rekonsiliasiBarang', 'rekonsiliasiBahanKemas', 'penimbanganBahanBaku','pemeriksaanQualityControl','pemeriksaanProsesProduksi', 'pemeriksaanPenimbang', 'quality', 'tahapanProses', 'pemeriksaanKebersihan', 'produksiSaatini'));
    }
    
    public function update(Request $request, $id) {
        $request->validate([
            'nama_ruangan' => 'required|string|max:255',
        ]);
    
        $room = RuangProduksi::findOrFail($id);
        $room->nama_ruangan = $request->nama_ruangan;
        $room->save();
    
        return response()->json(['success' => true]);
    
    }

    public function showRiwayatProduksi() {
        $tikets = Produksi::whereNotIn('status', ['pending', 'confirm'])->with('product', 'bahanBaku', 'bahanKemas', 'invoice', 'produkJadi')
        ->get()
        ->groupBy(function ($item) {
            // Buat pengelompokan berdasarkan tanggal
            return $item->tanggal_produksi;
        })
        ->sortKeysDesc()
        ->flatMap(function ($items) {
            // Di dalam tanggal yang sama, urutkan yang bisa di-download duluan
            return $items->sortByDesc(function ($item) {
                // status close/done di atas, failed di bawah.
                if (in_array($item->status, ['close', 'done'])) {
                    return 1;
                } elseif ($item->status === 'failed') {
                    return 2;
                } else {
                    return 0;
                }
            })->values();
        });
        return view('produksi.riwayat-produksi', compact( 'tikets' ));
    }

    public function showRiwayatAlatProduksi() {
        $riwayatAlat = RiwayatAlat::with('produksi', 'ruangProduksi')->get();
        $pemeriksaanKebersihan = PemeriksaanKebersihan::with([
            'bagianKebersihan.ruangProduksi',
            'produksi'
        ])->get();

        // Mapping alat ke ruangan alternatif
        $alatMapping = [
            'Tangki Mixing' => 'Ruang Filling',
            'Mesin Mixing'  => 'Ruang Mixing',
            'Mesin Filling' => 'Ruang Filling',
        ];

        // Susun map kebersihan berdasarkan produksi_id + ruang_produksi_id + nama alat
        $mapKebersihan = $pemeriksaanKebersihan->mapWithKeys(function ($item) {
            $alatName = $item->bagianKebersihan->nama_bagian ?? null;
            $key = $item->produksi_id . '-' . $item->bagianKebersihan->ruang_produksi_id . '-' . $alatName;

            return [
                $key => [
                    'alat'            => $alatName,
                    'dibersihkan_oleh'=> $item->dibersihkan_oleh,
                    'diperiksa_oleh'  => $item->diperiksa_oleh,
                ]
            ];
        });


        $riwayatAlat = $riwayatAlat->map(function ($alat) use ($mapKebersihan, $alatMapping) {
            // nama alat asli dari riwayat
            $alatNameRaw = $alat->nama_alat ?? null;

            // hapus angka di akhir biar bisa dicocokin dengan bagian kebersihan
            $alatName = preg_replace('/\s*\d+$/', '', $alatNameRaw);

            // cek mapping ruangan alternatif
            $ruangId = $alat->ruang_produksi_id;
            if (isset($alatMapping[$alatName])) {
                // ambil ruang_produksi_id sesuai mapping (cari berdasarkan nama ruangan)
                $ruangId = RuangProduksi::where('nama_ruangan', $alatMapping[$alatName])->value('id') ?? $ruangId;
            }

            // bikin key untuk dicocokin
            $key = $alat->produksi_id . '-' . $ruangId . '-' . $alatName;

            if (isset($mapKebersihan[$key])) {
                $alat->dibersihkan_oleh = $mapKebersihan[$key]['dibersihkan_oleh'];
                $alat->diperiksa_oleh   = $mapKebersihan[$key]['diperiksa_oleh'];
            } else {
                $alat->dibersihkan_oleh = 'Belum Dibersihkan';
                $alat->diperiksa_oleh   = 'Belum Diperiksa. Segera lapor ke QC';
            }

            return $alat;
        });

        return view('produksi.riwayat-alat-produksi', compact('riwayatAlat'));
    }

    function createRuangan(Request $request) {
        $data = $request->all();
        RuangProduksi::create($data);
        return redirect()->back()->with('success', 'Ruangan Berhasil Di Tambbhkan');
    }

    function deleteItem($id) {
        $item = BagianKebersihan::find($id);
        $item->delete();
        return redirect()->back();
    }
}