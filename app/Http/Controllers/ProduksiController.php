<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\MaterialProduksi;
use App\Inventory;
use App\Produksi;
use App\FormulaProduksi;
use App\AcuanLaporanIpc;
use App\CheckSerahTerimaBarang;
use App\Product;
use App\QualityControlCheck;
use App\StockOut;
use App\RiwayatAlat;
use App\StockInProduct;
use App\Transaction;
use App\ItemPpj;
use App\SampleInProses;
use App\InspeksiProses;
use App\FormulaBahanBaku;
use App\FormulaBahanKemas;
use App\ItemInspeksiProses;
use App\QualityControl;
use App\ItemReportInPc;
use App\Invoice;
use App\PengujianProdukJadi;
use App\PengambilanSampleProdukJadi;
use App\ReportInProcessControl;
use App\Order;
use App\FormulaPcs;
use App\Brand;

use Carbon\Carbon;


class ProduksiController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    function index() {
        $tikets = Produksi::all();
        $tiketsjadwal = Produksi::whereNull('tanggal_produksi')->get(); 
        return view('produksi.index-produksi', compact('tikets', 'tiketsjadwal'));
    }

    function create() {
        $product = Product::all();
        $invoice = Invoice::whereIn('status', ['accept', 'process'])
            ->with(['orders.product.produkjadi', 'orders.product.brand'])
            ->get()
            ->filter(function ($inv) {
                return $inv->orders->contains(function ($order) {
                    $sisa = $order->quantity - ($order->inProses ?? 0) - ($order->sendQty ?? 0);
                    return $sisa > 0;
                });
            });
        $inventory = Inventory::with('stockIn', 'stockOut')->get();

        $formulabahanbaku = FormulaBahanBaku::get();
        $formulabahankemas = FormulaBahanKemas::get();
    
        return view('produksi.create-produksi', compact('product', 'invoice', 'inventory', 'formulabahanbaku', 'formulabahankemas'));
    }

    function store(Request $request) {
        $data = $request->all();
        $formulaKemasan = $data['formula_bahan_kemas_id'];
        $formulaBahanBaku = $data['formula_bahan_baku_id'];
        $invoiceId = $data['invoice_id']; 
        $hasilJadi = array_sum($data['total_barang_jadi']);

    
        $invoice = Invoice::where('id', $request->input('invoice_id'))->first();
        $invoice->update(['status' => 'process']);
        
        foreach ($data['batch_number'] as $key => $batchNumber) {
            $batchSize = $data['batch_size'][$key];
            $produksiData = [
                'batch_number' => $batchNumber,
                'product_id' => $data['product_id'][$key],
                'invoice_id' => $invoiceId,
                'batch_size' => $batchSize,
                'total_barang_jadi' => $data['total_barang_jadi'][$key],
                'tanggal_expired' => $data['tanggal_expired'][$key],
            ];

            if ($batchSize != 100) {
                $produksiData['formula_bahan_kemas_id'] = $formulaKemasan;
                $produksiData['formula_bahan_baku_id'] = $formulaBahanBaku;
            } else {
                $produksiData['formula_bahan_kemas_id'] = $formulaKemasan;
                $produksiData['formula_bahan_baku_id'] = $formulaBahanBaku;
            }

            Produksi::create($produksiData);
        }

        // Ambil produk yang sama dengan produk id yang di ambil dari data
        $orderUpdate = Order::where('invoice_id', $invoiceId)
            ->where('product_id', $data['product_id'][0]) 
            ->first();

        $orderUpdate->inProses = $orderUpdate->inProses + $hasilJadi;

        $sisaQuantity = $orderUpdate->quantity - ($orderUpdate->inProses ?? 0) - ($orderUpdate->sendQty ?? 0);

        if ($sisaQuantity <= 0) {
            $orderUpdate->status = 'process';
        } else{
            $orderUpdate->status = 'process';
        }

        $orderUpdate->save();
    
        return redirect('/ruang-produksi')->with('success', 'Tiket berhasil dibuat');
    }

    public function storeKhusus(Request $request) {
        $data = $request->all();
        $formulaKemasan = $data['formula_bahan_kemas_id'] ?? null;
        $formulaBahanBaku = $data['formula_bahan_baku_id'] ?? null;
        $keterangan = $data['keterangan'];
        $isCustomFormula = isset($data['is_custom_formula']); // checkbox biasanya cuma kirim saat dicentang

        foreach ($data['batch_number'] as $key => $batchNumber) {
            $batchSize = $data['batch_size'][$key];

            $produksiData = [
                'batch_number' => $batchNumber,
                'product_id' => $data['product_id'][$key],
                'batch_size' => $batchSize,
                'keterangan' => $keterangan,
                'total_barang_jadi' => $data['total_barang_jadi'][$key],
                'tanggal_expired' => $data['tanggal_expired'][$key],
                'is_custom_formula' => $isCustomFormula,
            ];

            if ($isCustomFormula) {
                $inventories = $data['custom_formula_kemas']['inventory_id'] ?? [];
                $jumlahs = $data['custom_formula_kemas']['jumlah'] ?? [];

                $customKemas = [];

                foreach ($inventories as $i => $inventoryId) {
                    if (!empty($inventoryId) && isset($jumlahs[$i])) {
                        $customKemas[] = [
                            'inventory_id' => $inventoryId,
                            'jumlah' => $jumlahs[$i],
                        ];
                    }
                }

                $produksiData['custom_formula_kemas'] = json_encode($customKemas);
                $produksiData['formula_bahan_kemas_id'] = $formulaKemasan;
                $produksiData['formula_bahan_baku_id'] = $formulaBahanBaku;
            } else {
                $produksiData['formula_bahan_kemas_id'] = $formulaKemasan;
                $produksiData['formula_bahan_baku_id'] = $formulaBahanBaku;
                $produksiData['custom_formula_kemas'] = null;
            }


            Produksi::create($produksiData);
        }

        return redirect('/ruang-produksi')->with('success', 'Tiket Khusus berhasil disimpan!');
    }

    public function detail($batch_number) {
        $produksi = Produksi::where('batch_number', $batch_number)->first();
        $inventories = Inventory::all();

        $formulaKemasan = [];
        $formulaBahanBaku = [];

        if($produksi->is_custom_formula !== 1){
            $formulaKemasan = \App\FormulaBahanKemas::where('formula_id', $produksi->formula_bahan_kemas_id)->get();
            $formulaBahanBaku = \App\FormulaBahanBaku::where('formula_id', $produksi->formula_bahan_baku_id)->get();
        }

        return view('produksi.detail-produksi', compact('produksi', 'inventories', 'formulaKemasan', 'formulaBahanBaku'));
    }
    
    public function confirmProduksi(Request $request, $id) {
        $produksi = Produksi::find($id);

        if (!$produksi) {
            return redirect()->back()->with('error', 'Produksi tidak ditemukan');
        }

        $data = $request->all();
        $data['status'] = 'confirm';

        $produksi->fill($data);

        if ($produksi->isDirty()) {
            $produksi->update();
        }

        $userId = auth()->user()->id;

        if ($produksi->is_custom_formula) {
            // Handle custom formula
            $customKemas = json_decode($produksi->custom_formula_kemas, true);

            foreach ($customKemas as $item) {
                $inventoryId = $item['inventory_id'] ?? null;
                $jumlah = $item['jumlah'] ?? 0;

                MaterialProduksi::create([
                    'produksi_id' => $produksi->id,
                    'formula_bahan_baku_id' => null,
                    'formula_bahan_kemas_id' => null,
                    'inventory_id' => $inventoryId,
                    'jumlah' => $jumlah,
                    'user_id' => $userId,
                    'persetujuan' => 'pending',
                ]);
            }

        } else {

            $batchSize = $produksi->batch_size ?? 100;

            $formulaBaku = FormulaBahanBaku::where('formula_id', $produksi->formula_bahan_baku_id)->get();

            foreach ($formulaBaku as $f) {

                $jumlah = ($f->jumlah / 100) * $batchSize;

                $jumlahFinal = smartRound($jumlah);

                MaterialProduksi::create([
                    'produksi_id' => $produksi->id,
                    'inventory_id' => $f->inventory_id,
                    'jumlah' => $jumlahFinal,
                    'formula_bahan_baku_id' => $f->id,
                    'user_id' => $userId,
                    'persetujuan' => 'pending',
                ]);
            }

            $formulaKemasan = FormulaBahanKemas::where('formula_id', $produksi->formula_bahan_kemas_id)->get();

            foreach ($formulaKemasan as $f) {

                $jumlah = ($f->jumlah / 100) * $batchSize;

                $jumlahFinal = ceil($jumlah);

                MaterialProduksi::create([
                    'produksi_id' => $produksi->id,
                    'inventory_id' => $f->inventory_id,
                    'jumlah' => $jumlahFinal,
                    'formula_bahan_kemas_id' => $f->id,
                    'user_id' => $userId,
                    'persetujuan' => 'pending',
                ]);
            }
        }

        return redirect()->route('detail.produksi', $produksi->batch_number);
    }

    public function updateEvent($id, Request $request){
        $event = Produksi::find($id);
    
        $event->tanggal_produksi = Carbon::parse($request->tanggal_produksi)->setTimezone('Asia/Jakarta');
        $event->save();
    
        return response()->json(['message' => 'Event updated successfully', 'event' => $event]);
    }
    

    public function getProducts($invoice_id) {
        $invoice = Invoice::with(['orders.product.produkjadi', 'orders.product.brand'])->find($invoice_id);

        if (!$invoice) {
            return response()->json(['error' => 'Invoice tidak ditemukan'], 404);
        }

        $orders = $invoice->orders;
        $products = [];

        foreach ($orders as $order) {
            if ($order->product) {
                $sisa = $order->quantity - ($order->inProses ?? 0) - ($order->sendQty ?? 0);
                
                // Skip kalau sisa <= 0
                if ($sisa <= 0) continue;

                $hasil_acuan = $order->product->produkjadi->acuan ?? 1;
                $batch_size = $order->product->produkjadi->size_batch ?? 100;
                $jumlah_tiket = ceil($sisa / $hasil_acuan);

                $products[] = [
                    'product_id' => $order->product->id,
                    'product_name' => $order->product->name,
                    'brand_name' => $order->product->brand->name,
                    'hasil_satu_batch' => $hasil_acuan,
                    'ukuran_batch' => $batch_size,
                    'code_product' => $order->product->code,
                    'jumlah_tersisa' => $sisa,
                    'jumlah_tiket' => $jumlah_tiket,
                    'invoice_id' => $invoice_id,
                ];
            }
        }

        return response()->json($products);
    }

    public function getFormulaByTemplate(Request $request) {
        $formulaId = $request->formula_id;

        // Ambil semua formula sesuai id formula
        $formulas = FormulaBahanBaku::where('formula_id', $formulaId)
            ->with(['inventory.stockIn', 'inventory.stockOut'])
            ->get();

        // Hitung stok akhir per formula
        $formulasWithStock = $formulas->map(function ($formula) {
            if ($formula->inventory) {
                $stokAwal   = $formula->inventory->stok ?? 0;
                $stokMasuk  = $formula->inventory->stockIn->sum('stok'); // total masuk
                $stokKeluar = $formula->inventory->stockOut->sum('jumlah'); // total keluar
                $stokAkhir  = $stokAwal + $stokMasuk - $stokKeluar;
            } else {
                $stokAkhir = 0;
            }

            // Tambahkan stok_akhir ke object formula
            return array_merge($formula->toArray(), [
                'stok_akhir' => $stokAkhir
            ]);
        });

        return response()->json($formulasWithStock);
    }

    public function getKemasanByTemplate(Request $request) {
        $formulaId = $request->formula_id;

        // Ambil semua formula kemasan sesuai list formula_id
        $formulas = FormulaBahanKemas::where('formula_id', $formulaId)
            ->with(['inventory.stockIn', 'inventory.stockOut'])
            ->get();

        // Hitung stok akhir per formula
        $formulasWithStock = $formulas->map(function ($formula) {
            if ($formula->inventory) {
                $stokAwal   = $formula->inventory->stok ?? 0;
                $stokMasuk  = $formula->inventory->stockIn->sum('stok'); // total masuk
                $stokKeluar = $formula->inventory->stockOut->sum('jumlah'); // total keluar
                $stokAkhir  = $stokAwal + $stokMasuk - $stokKeluar;
            } else {
                $stokAkhir = 0;
            }

            // Tambahkan stok_akhir ke object formula
            return array_merge($formula->toArray(), [
                'stok_akhir' => $stokAkhir
            ]);
        });

        return response()->json($formulasWithStock);
    }

    public function getFormulaTemplatesByProduct($productId) {
        $product = Product::find($productId);

        $cleanName = preg_replace('/\b\d+\s*(ml|l|mg|g|kg)\b/i', '', $product->name);
        $cleanName = trim($cleanName);

        // cari nama formula yang MIRIP dengan nama produk
        $templates = \App\Formula::where('tipe', 'bahan_baku')
            ->where('nama_formula', 'LIKE', '%' . $cleanName . '%')
            ->get();

        return response()->json($templates);
    }

    public function getBahanKemasByTemplate($productId) {
        $product = Product::find($productId);

        $cleanName = preg_replace('/\b\d+\s*(ml|l|mg|g|kg)\b/i', '', $product->name);
        $cleanName = trim($cleanName);

        // cari nama formula yang MIRIP dengan nama produk
        $templates = \App\Formula::where('tipe', 'bahan_kemas')
            ->where('nama_formula', 'LIKE', '%' . $cleanName . '%')
            ->get();


        return response()->json($templates);
    }

    // dibutuhkan di view
    public function getLastProductionNumber(Request $request) {
        $year = date('Y'); // Tahun saat ini
        $lastProduction = DB::table('produksi')
                            ->where('batch_number', 'like', substr($year, -3) . '%') // Ambil batch untuk tahun ini
                            ->orderBy('batch_number', 'desc')
                            ->first();
    
        if ($lastProduction) {
            // Ambil 3 digit terakhir dari batch_number
            $lastProductionNumber = intval(substr($lastProduction->batch_number, -3)); 
            return response()->json(['success' => true, 'lastProductionNumber' => $lastProductionNumber]);
        }
    
        // Jika tidak ada, mulai dari 0
        return response()->json(['success' => true, 'lastProductionNumber' => 0]);
    }

    // QC Controller
    public function indexQC() {
        $tikets = Produksi::with(['product', 'produkJadi', 'checkQualityControl', 'pemantauanStabilitas.detail'])
        ->get()
        ->sortByDesc(function ($item) {
            return $item->tanggal_produksi;
        })
        ->groupBy(function ($item) {
            // Buat pengelompokan berdasarkan tanggal
            return $item->tanggal_produksi;
        })
        ->flatMap(function ($items) {
            // Di dalam tanggal yang sama, urutkan yang bisa di-download duluan
            return $items->sortByDesc(function ($item) {
                return $item->checkQualityControl->first() ? 1 : 0;
            });
        });
        return view('produksi.riwayat-qc', compact('tikets'));
    }
    
    function show($date){
        // Ambil data tiket berdasarkan tanggal beserta relasi product dan barangJadi
        $tikets = Produksi::with(['product', 'barangJadi', 'produkJadi'])
            ->whereDate('tanggal_produksi', $date)
            ->get();

        // Tampilkan view dengan data tiket 
        return view('produksi.detail-list-tiket', compact('tikets', 'date'));
    }

    function kirimBarangJadi(Request $request, $id) {
        $produksi = Produksi::find($id);
        CheckSerahTerimaBarang::create($request->all());

        StockInProduct::create([
            'produksi_id' => $produksi->id,
            'product_id' => $produksi->product->id,
            'quantity' => $request->nilai_actual,
            'note' => 'Produksi Batch Number ' . $produksi->batch_number . '. ED :' . $produksi->tanggal_expired,
        ]);

        // Update Status
        $produksi->status = 'close';
        $produksi->save(); 

    
        return redirect()->back()->with('success', 'Barang berhasil dikirim.');
    }

    public function updateBarangJadi(Request $request, $id) {
        $request->validate([
            'nilai_actual' => 'required|numeric',
        ]);

        $produksi = Produksi::findOrFail($id);

        $barangJadi = CheckSerahTerimaBarang::where('produksi_id', $produksi->id)->first();
        if ($barangJadi) {
            $barangJadi->update([
                'nilai_actual' => $request->nilai_actual,
            ]);
        } else {
            CheckSerahTerimaBarang::create([
                'produksi_id' => $produksi->id,
                'nilai_actual' => $request->nilai_actual,
                'operator' => auth()->user()->name ?? null,
            ]);
        }

        // Sinkronisasi quantity pada StockInProduct jika ada
        $stockIn = StockInProduct::where('produksi_id', $produksi->id)->first();
        if ($stockIn) {
            $stockIn->update([
                'quantity' => $request->nilai_actual,
            ]);
        }

        return redirect()->back()->with('success', 'Nilai actual barang jadi berhasil diperbarui.');
    }

    public function updateInProses(Request $request, $id) {
        $order = Order::findOrFail($id);
        $order->inProses = $request->input('inProses');
        $order->save();

        return response()->json(['success' => true, 'data' => $order]);
    }
    
    public function updateSendQty(Request $request, $id) {
        $order = Order::findOrFail($id);
        $order->sendQty = $request->input('sendQty');
        $order->save();
    
        return response()->json(['success' => true, 'data' => $order]);
    }    

    public function deleteProduksiTiket(Request $request) {
        $id = $request->id;

        $produksi = Produksi::with('invoice')->find($id);

        if (!$produksi) {
            return redirect()->back()->with('error', 'Tiket Produksi tidak ditemukan');
        }

        $hasilJadi = $produksi->total_barang_jadi;

        if ($produksi->invoice_id) {
            $orderUpdate = Order::where('invoice_id', $produksi->invoice_id)
                ->where('product_id', $produksi->product_id) 
                ->first();

            if ($orderUpdate) {
                $orderUpdate->inProses = ($orderUpdate->inProses ?? 0) - $hasilJadi;

                $sisaQuantity = $orderUpdate->quantity 
                    - ($orderUpdate->inProses ?? 0) 
                    - ($orderUpdate->sendQty ?? 0);

                // Ubah status
                if ($sisaQuantity > 0) {
                    $orderUpdate->status = 'process';
                } else {
                    $orderUpdate->status = 'done';
                }

                $orderUpdate->save();
            }
        }

        $materialProduksi = MaterialProduksi::where('produksi_id', $id)->get();
        foreach ($materialProduksi as $material) {
            StockOut::where('permintaan_id', $material->id)->delete();
            $material->delete();
        }

        $riwayatAlat = RiwayatAlat::where('produksi_id', $id)->get();
        foreach ($riwayatAlat as $riwayat) {
            $riwayat->delete();
        }

        // Delete produksi tiket
        $produksi->delete();

        return redirect('/ruang-produksi')->with('success', 'Tiket berhasil dihapus');
    }

    public function sampleIPC() {
        // ambil data produksi yang dimana checkProses nya sudah ada
        $data = Produksi::where('status', '!=', 'pending')->with('product')->get();

        foreach ($data as $item) {
            $produksiId = $item->id;

            $hasSampleIPC = SampleInProses::where('produksi_id', $produksiId)->exists();
            $hasInspeksiData = InspeksiProses::where('produksi_id', $produksiId)->exists();
            $hasReportData = ReportInProcessControl::where('produksi_id', $produksiId)->exists();
            $hasPengambilanSample = PengambilanSampleProdukJadi::where('produksi_id', $produksiId)->exists();
            $hasPengujianProdukJadi = PengujianProdukJadi::where('produksi_id', $produksiId)->exists();

            // bikin properti baru sementara
            $item->cetakLaporanIPC = $hasSampleIPC && $hasInspeksiData && $hasReportData && $hasPengambilanSample && $hasPengujianProdukJadi;
        }
        return view('produksi.pengambilan-sample-ipc', compact('data'));
    }

    public function viewFormSampleIPC($batch_number) {
        $produksi = Produksi::where('batch_number', $batch_number)->with('product')->first();
        
        $inspeksi = ItemInspeksiProses::where('produksi_id', $produksi->id)->get();
        $report = ItemReportInPc::where('produksi_id', $produksi->id)->get();
        $ppj = ItemPpj::where('produksi_id', $produksi->id)->get();

        $barangJadi = CheckSerahTerimaBarang::where('produksi_id', $produksi->id)->first();

        $acuanPengujian = AcuanLaporanIpc::where('formula_id', $produksi->formula_bahan_baku_id)->get();

        $sampleIPC = SampleInProses::where('produksi_id', $produksi->id)->first();
        $inspeksiData = InspeksiProses::where('produksi_id', $produksi->id)->first();
        $reportData = ReportInProcessControl::where('produksi_id', $produksi->id)->first();
        $pengambilanSample = PengambilanSampleProdukJadi::where('produksi_id', $produksi->id)->first();
        $pengujianProdukJadi = PengujianProdukJadi::where('produksi_id', $produksi->id)->first();
        return view('produksi.view-form-sample-ipc', compact('barangJadi', 'produksi', 'inspeksi', 'report', 'ppj', 'acuanPengujian', 'sampleIPC', 'pengambilanSample', 'pengujianProdukJadi', 'inspeksiData', 'reportData'));
    }
}