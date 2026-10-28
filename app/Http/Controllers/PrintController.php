<?php

namespace App\Http\Controllers;

use App\PemantauanStabilitas;
use App\DetailPemantauanStabilitas;
use Illuminate\Http\Request;
use App\Invoice;
use App\Order;
use Illuminate\Support\Facades\Auth;
use App\Product;
use Carbon\Carbon;
use App\Produksi;
use App\PemeriksaanKebersihan;
use App\RiwayatAlat;
use App\ItemInspeksiProses;
use App\FormulaBahanKemas;
use App\Deliveries;
use App\FormulaBahanBaku;
use App\ItemReportInPc;
use App\ItemPpj;
use App\AcuanLaporanIpc;
use App\PenimbanganBahanBaku;
use App\CheckTahapanProses;
use App\CheckSerahTerimaBarang;
use App\StockInProduct;
use App\MaterialProduksi;
use App\StockOutProduct;
use App\RekonsiliasiBahanKemas;
use App\SerahTerimaBarang;
use App\Inventory;
use App\StockIn;
use App\StockOut;
use App\QualityControl;
use App\QualityControlCheck;
use App\RuangProduksi;
use App\SampleInProses;
use App\InspeksiProses;
use App\ReportInProcessControl;
use App\PengambilanSampleProdukJadi;
use App\PengujianProdukJadi;
use PDF;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf as PdfWriter;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;
use Illuminate\Support\Collection;

class PrintController extends Controller
{
    function preorder($slug) {
        $invoice = Invoice::where('slug', $slug)->first();
        $orders = Order::where('invoice_id', $invoice->id)->get();
        $orderInformation = Order::where('invoice_id', $invoice->id)->first();

        $pdf = PDF::loadView('print.print-preorder', ['invoice' => $invoice,'orders' => $orders, 'orderInformation' => $orderInformation]);
        return $pdf->stream('PreOrderDocument.pdf', ['Content-Type' => 'application/pdf']);
    }

    function rekaporder(Request $request) {
        $fromMonth = Carbon::parse($request->input('fromMonth'));
        $toMonth = Carbon::parse($request->input('toMonth'));

        if(Auth::user()->hasRole('Customer')){
            $orders = Order::wheredate('created_at', '>=', $fromMonth)
                    ->wheredate('created_at', '<=', $toMonth)
                    ->where('id_user', Auth::user()->id)
                    ->get();
        }else{
            $orders = Order::wheredate('created_at', '>=', $fromMonth)
                    ->wheredate('created_at', '<=', $toMonth)
                    ->get();
        }

        $uniqueProducts = [];

        // Loop melalui semua pesanan
        foreach ($orders as $order) {

            if ($order->status == 'reject') {
                continue;
            }
            
            $productName = $order->product->name;
            $productPriceString = $order->price; // Harga produk sebagai string
            $productPrice = (float) str_replace(['Rp ', '.', ], '', $productPriceString); // Menghapus karakter non-numerik dan mengonversi ke float
            $productSlug = $order->invoice->slug;
            $productCode = $order->product->code;
            $quantity = $order->quantity;
            $totalPrice = $productPrice * $quantity;

            // Jika produk sudah ada dalam array, tambahkan jumlah dan total harga
            if (array_key_exists($productName, $uniqueProducts)) {
                $uniqueProducts[$productName]['quantity'] += $quantity;
                $uniqueProducts[$productName]['code'] == $productCode;
                $uniqueProducts[$productName]['slug'] == $productSlug;
                $uniqueProducts[$productName]['price'] == $productPrice;
                $uniqueProducts[$productName]['totalPrice'] += $totalPrice;
            } else {
                // Jika produk belum ada dalam array, tambahkan produk beserta jumlah dan total harga
                $uniqueProducts[$productName] = [
                    'quantity' => $quantity,
                    'code' => $productCode,
                    'slug' => $productSlug,
                    'price' => $productPrice,
                    'totalPrice' => $totalPrice
                ];
            }
        }

        $uniqueProductsWithPO = [];

        foreach ($orders as $order) {
            if ($order->status == 'reject') {
                continue;
            }

            $productName = $order->product->name;
            $productCode = $order->product->code;
            $productQuantity = $order->quantity;
            $productStatus = $order->status;
            $productPONumber = $order->invoice->nomor_invoice;

            // Jika produk sudah ada dalam array
            if (array_key_exists($productName, $uniqueProductsWithPO)) {
                // Cek apakah nomor invoice sudah ada dalam array
                $index = array_search($productPONumber, $uniqueProductsWithPO[$productName]['nomor_invoice']);
                if ($index !== false) {
                    // Jika nomor invoice sudah ada, tambahkan jumlah dan status ke indeks yang sesuai
                    $uniqueProductsWithPO[$productName]['quantity'][$index] += $productQuantity;
                    $uniqueProductsWithPO[$productName]['status'][$index] = $productStatus;
                } else {
                    // Jika nomor invoice belum ada, tambahkan nomor invoice, jumlah, dan status baru
                    $uniqueProductsWithPO[$productName]['nomor_invoice'][] = $productPONumber;
                    $uniqueProductsWithPO[$productName]['quantity'][] = $productQuantity;
                    $uniqueProductsWithPO[$productName]['status'][] = $productStatus;
                }
            } else {
                $uniqueProductsWithPO[$productName] = [
                    'nomor_invoice' => [$productPONumber],
                    'code' => $productCode,
                    'quantity' => [$productQuantity],
                    'status' => [$productStatus]
                ];
            }
        }

        $pdf = PDF::loadView('print.print-rekap', ['uniqueProducts' => $uniqueProducts, 'uniqueProductsWithPO' => $uniqueProductsWithPO, 'orders' => $orders,'fromMonth' => $fromMonth, 'toMonth' => $toMonth]);
        return $pdf->stream('Rekap Order.pdf' , ['Content-Type' => 'application/pdf']);
    }

    function unpaid() {
        $order = Order::query();
        $instansiId = Auth::user()->id;
        $orderData = null;
        
        if (Auth::user()->hasRole('Customer')) {
            $order->whereHas('invoice', function ($query) use ($instansiId) {
                $query->where('id_user', $instansiId);
            });
            
            $orderData = Order::where('id_user', $instansiId)
            ->where(function($query) {
                $query->where('status', '!=', 'paid')
                      ->orWhere('status', '!=', 'done');
            })
            ->get();

        }else{ 
            $orderData = Order::all();
        }

        $pdf = PDF::loadView('print.print-unpaind', ['orderData' => $orderData]);
        return $pdf->stream('Unpaid Order Document' , ['Content-Type' => 'application/pdf']);
    }


    public function laporanProduksi($batchnumber) {
        $produksi = Produksi::with([
                'bahanBaku',
                'bahanKemas',
                'product'
            ])->where('batch_number', $batchnumber)->first();


        if ($produksi) {
            $produksi_sebelumnya = Produksi::where('tanggal_produksi', $produksi->tanggal_produksi)
                ->where('batch_number', '<', $produksi->batch_number)
                ->orderBy('batch_number', 'desc')
                ->with('product')
                ->first();
        
            if (!$produksi_sebelumnya) {
                $produksi_sebelumnya = Produksi::where('tanggal_produksi', '<', $produksi->tanggal_produksi)
                    ->orderBy('tanggal_produksi', 'desc')
                    ->orderBy('batch_number', 'desc')
                    ->with('product')
                    ->first();
            }
        } else {
            $produksi_sebelumnya = null;
        }


        // Ambil semua data terkait berdasarkan produksi_id
        $kebersihan = PemeriksaanKebersihan::with('bagianKebersihan.ruangProduksi')
                ->where('produksi_id', $produksi->id)
                ->get();

        $bahanKemas = [];
        $bahanBaku = [];        

        if($produksi->is_custom_formula !== 1){
            $bahanKemas = FormulaBahanKemas::where(
                    'formula_id',
                    $produksi->formula_bahan_kemas_id
                )->get();

            $bahanBaku = FormulaBahanBaku::where(
                    'formula_id',
                    $produksi->formula_bahan_baku_id
                )->get();
        }

        $penimbangan = PenimbanganBahanBaku::where('produksi_id', $produksi->id)->get();
        $tahapanProses = CheckTahapanProses::where('produksi_id', $produksi->id)->get();
        $dataQualityControl = QualityControlCheck::where('produksi_id', $produksi->id)->get();
        $rekonsiliasi = RekonsiliasiBahanKemas::with('bahanKemas')->where('produksi_id', $produksi->id)->get();

        $customKemas = collect(json_decode($produksi->custom_formula_kemas ?? '[]', true));
        $inventoryMap = Inventory::whereIn(
                'id',
                $customKemas->pluck('inventory_id')
            )
            ->get()
            ->keyBy('id'); 

        $kemasFinal = $customKemas->map(function ($item) use ($inventoryMap) {
            return [
                'inventory_id' => $item['inventory_id'],
                'name'         => $inventoryMap[$item['inventory_id']]->name ?? 'Tidak ditemukan',
                'jumlah'       => $item['jumlah'],
            ];
        });


        $aucanhasil = SerahTerimaBarang::where('product_id', $produksi->product->id)->first();
        $hasilproduksi = CheckSerahTerimaBarang::where('produksi_id', $produksi->id)->first();
        
        $previousDataByRoom = [];

        $riwayatAlatCurrent = RiwayatAlat::with('ruangProduksi')
            ->where('produksi_id', $produksi->id)
            ->get();

        $riwayatAlatByRoom = $riwayatAlatCurrent->groupBy(function($item) {
            return $item->ruangProduksi->nama_ruangan ?? 'Unassigned';
        });

        foreach ($riwayatAlatByRoom as $namaRuangan => $alatList) {
            $alatNames = $alatList->pluck('nama_alat')->unique()->filter()->values();

            if ($alatNames->isEmpty()) {
                continue;
            }

            $prevProduksiCandidate = Produksi::where('id', '!=', $produksi->id)
                ->whereHas('riwayatAlat', function($q) use ($alatNames) {
                    $q->whereIn('nama_alat', $alatNames);
                })
                ->where(function($q) use ($produksi) {
                    $q->where('tanggal_produksi', '<', $produksi->tanggal_produksi)
                      ->orWhere(function($q2) use ($produksi) {
                          $q2->where('tanggal_produksi', '=', $produksi->tanggal_produksi)
                             ->where('batch_number', '<', $produksi->batch_number);
                      });
                })
                ->with('product')
                ->orderBy('tanggal_produksi', 'desc')
                ->orderBy('batch_number', 'desc')
                ->first();

            if ($prevProduksiCandidate) {
                $previousDataByRoom[$namaRuangan] = $prevProduksiCandidate;
            }
        }
        
        // Kirim ke view
        $pdf = Pdf::loadView('print.hasil-produksi', [
            'produksi' => $produksi,
            'kebersihan' => $kebersihan,
            'penimbangan' => $penimbangan,
            'tahapanProses' => $tahapanProses,
            'qualityControl' => $dataQualityControl,
            'rekonsiliasi' => $rekonsiliasi,
            'hasilproduksi' => $hasilproduksi,
            'hasilAcuan' => $aucanhasil,
            'produksi_sebelumnya' => $produksi_sebelumnya,
            'previousDataByRoom' => $previousDataByRoom, // Variable baru
            'kemasFinal' => $kemasFinal,
            'bahanKemas' => $bahanKemas,
            'bahanBaku' => $bahanBaku
        ]);

        return $pdf->stream('Laporan Produksi - ' . $batchnumber . '.pdf', ['Content-Type' => 'application/pdf']);
    }
    
    public function serfifikatQc($batchnumber) {
        $produksi = Produksi::where('batch_number', $batchnumber)->firstOrFail();

        $dataQC = QualityControlCheck::where('produksi_id', $produksi->id)
                    ->with('qualityControl') 
                    ->get();

        $dataQC = $dataQC->sortBy(function ($item) {
            return $item->qualityControl->urutan ?? 9999; 
        })->values(); 

        $pdf = PDF::loadView('print.print-sertifikat-analis', [
            'dataQC' => $dataQC,
            'batchnumber' => $batchnumber,
            'produksi' => $produksi
        ]);

        return $pdf->stream('Sertifikat Analis - ' . $batchnumber . '.pdf', [
            'Content-Type' => 'application/pdf'
        ]);
    }

    public function printRiwayatAlat(Request $request) {
        $alat  = $request->input('alat');
        $bulan = $request->input('bulan');

        // Helper: normalisasi nama alat (hapus angka di belakang)
        $normalizeNamaAlat = function ($nama) {
            return preg_replace('/\s*\d+$/', '', $nama); // Mesin Mixing 1 -> Mesin Mixing
        };

        // Ambil data riwayat
        $riwayatAlat = RiwayatAlat::with(['produksi.product', 'ruangProduksi'])
            ->when($alat, fn($q) => $q->where('nama_alat', $alat))
            ->when($bulan, fn($q) => $q->whereMonth('created_at', date('m', strtotime($bulan)))
                                        ->whereYear('created_at', date('Y', strtotime($bulan))))
            ->get();

        // Ambil data pemeriksaan kebersihan
        $pemeriksaanKebersihan = PemeriksaanKebersihan::with(['bagianKebersihan.ruangProduksi', 'produksi'])
            ->get();

        // Mapping pemeriksaan kebersihan berdasarkan produksi_id + ruang_produksi_id + nama_alat (dinormalisasi)
        $mapKebersihan = $pemeriksaanKebersihan->mapWithKeys(function ($item) use ($normalizeNamaAlat) {
            $alatName = $normalizeNamaAlat($item->bagianKebersihan->nama_bagian);
            $key = $item->produksi_id . '-' . $item->bagianKebersihan->ruang_produksi_id . '-' . $alatName;

            return [
                $key => [
                    'dibersihkan_oleh' => $item->dibersihkan_oleh,
                    'diperiksa_oleh'   => $item->diperiksa_oleh,
                ]
            ];
        });

        // Opsional: mapping ruangan alternatif (misalnya Tangki Mixing dicatat di ruang Filling, bukan Mixing)
        $alatMapping = [
            'Tangki Mixing' => 'Ruang Filling',
        ];

        // Gabungkan hasil mapping ke riwayat alat
        $riwayatAlat = $riwayatAlat->map(function ($alat) use ($mapKebersihan, $normalizeNamaAlat, $alatMapping) {
            $alatName = $normalizeNamaAlat($alat->nama_alat);
            $ruangId  = $alat->ruang_produksi_id;

            // kalau ada mapping ruangan khusus
            if (isset($alatMapping[$alatName])) {
                $ruangId = RuangProduksi::where('nama_ruangan', $alatMapping[$alatName])->value('id') ?? $ruangId;
            }

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

        $rowsPerPage = 28; 
        $chunks = $riwayatAlat->chunk($rowsPerPage);

        // if no data, add an empty chunk to show one page with blanks
        if ($chunks->isEmpty()) {
            $chunks = collect([collect([])]);
        }

        // Kirim ke view PDF
        $pdf = Pdf::loadView('print.print-checklist-alat', [
            'chunks'       => $chunks,
            'alat'         => $alat,
            'bulan'        => $bulan,
            'rowsPerPage'  => $rowsPerPage,
        ])->setPaper('A4', 'portrait');

        return $pdf->stream('Checklist-Alat.pdf');
    }

    // coba update server
    public function laporanIpc($batch_number) {
        $produksi = Produksi::where('batch_number', $batch_number)->with('product')->first();
        
        $inspeksi = ItemInspeksiProses::where('produksi_id', $produksi->id)->get();
        $report = ItemReportInPc::where('produksi_id', $produksi->id)->get();
        $ppj = ItemPpj::where('produksi_id', $produksi->id)->get();

        $acuanPengujian = AcuanLaporanIpc::where('formula_id', $produksi->formula_id)->get();
        $barangJadi = CheckSerahTerimaBarang::where('produksi_id', $produksi->id)->first();

        $sampleIPC = SampleInProses::where('produksi_id', $produksi->id)->first();
        $inspeksiData = InspeksiProses::where('produksi_id', $produksi->id)->first();
        $reportData = ReportInProcessControl::where('produksi_id', $produksi->id)->first();
        $pengambilanSample = PengambilanSampleProdukJadi::where('produksi_id', $produksi->id)->first();
        $pengujianProdukJadi = PengujianProdukJadi::where('produksi_id', $produksi->id)->first();


        $pdf1 = Pdf::loadView('print.laporan-ipc.page-one-two', [
            'produksi' => $produksi,
            'sampleIPC' => $sampleIPC,
            'inspeksiData' => $inspeksiData,
            'inspeksi' => $inspeksi,
        ])->setPaper('a4', 'portrait')->output();

        $pdf2 = Pdf::loadView('print.laporan-ipc.page-three', [
            'produksi' => $produksi,
            'reportData' => $reportData,
            'report' => $report,
        ])->setPaper('a4', 'landscape')->output();

        $pdf3 = Pdf::loadView('print.laporan-ipc.page-four-five', [
            'produksi' => $produksi,            
            'pengambilanSample' => $pengambilanSample,
            'pengujianProdukJadi' => $pengujianProdukJadi,
            'barangJadi' => $barangJadi,
            'ppj' => $ppj,
        ])->setPaper('a4', 'portrait')->output();

        $merger = PDFMerger::init();
        $merger->addString($pdf1);
        $merger->addString($pdf2);
        $merger->addString($pdf3);
        $merger->merge();

        $pdfOutput = $merger->output();

        $filename = 'laporan ipc ' . $produksi->batch_number . '.pdf';

        return response($pdfOutput, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="'.$filename.'"');
    }

    public function kartuStok($slug) {
        ini_set('memory_limit', '512M');
        set_time_limit(300); // set time limit ke 5 menit

        $inventory = Inventory::where('slug', $slug)->firstOrFail();

        $stockIn = StockIn::where('inventory_id', $inventory->id)
            ->get();

        $totalIn = $stockIn->sum('stok');

        $stockOut = StockOut::where('inventory_id', $inventory->id)
            ->with(['stockIn', 'permintaanMaterial.produksi'])
            ->get();

        $totalOut = $stockOut->sum('jumlah');

        $stokAwal = $inventory->stok;
        $stokAkhir = $stokAwal + $totalIn - $totalOut;
        
        $mutasi = collect();

        foreach ($stockIn as $in) {
            $mutasi->push([
                'tanggal' => $in->tanggal_masuk,
                'created_at' => $in->created_at,
                'ed' => $in->tanggal_ed ? $in->tanggal_ed->format('d-m-Y') : '-',
                'keterangan' => 'Stock Masuk',
                'batch' => $in->nomor_batch ?? '-',
                'masuk' => $in->stok,
                'keluar' => 0,
                'jenis' => 'in',
            ]);
        }

        foreach ($stockOut as $out) {
            $mutasi->push([
                'tanggal' => $out->tanggal_keluar,
                'created_at' => $out->created_at,
                'keterangan' => $out->keterangan ?? optional(optional($out->permintaanMaterial)->produksi)->batch_number ?? 'Stock Keluar',
                'batch' => optional($out->stockIn)->nomor_batch ?? '-',
                'ed' => optional($out->stockIn)->tanggal_ed ? optional($out->stockIn)->tanggal_ed->format('d-m-Y') : '-',
                'masuk' => 0,
                'keluar' => $out->jumlah,
                'jenis' => 'out',
            ]);
        }

        // Urutkan berdasarkan tanggal, lalu jenis (in sebelum out), lalu created_at
        $mutasi = $mutasi->sortBy(function ($item) {
            $typeOrder = $item['jenis'] == 'in' ? 0 : 1;
            return sprintf(
                '%s-%d-%s',
                $item['tanggal']->format('Y-m-d'),
                $typeOrder,
                $item['created_at']->timestamp
            );
        })->values();

        // Hitung running_sisa
        $runningSisa = $stokAwal;
        $mutasi = $mutasi->map(function ($row) use (&$runningSisa) {
            $runningSisa += $row['masuk'];
            $runningSisa -= $row['keluar'];
            $row['running_sisa'] = $runningSisa;
            return $row;
        });

        $rowsPerPage = 22;
        $chunks = $mutasi->chunk($rowsPerPage);

        $pdf = PDF::loadView('print.print-kartu-stok', [
            'inventory'  => $inventory,
            'stokAwal'   => $stokAwal,
            'stokAkhir'  => $stokAkhir,
            'chunks'     => $chunks,
        ]);

        return $pdf->stream(
            'Kartu-Stok-' . $inventory->name . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    public function kartuStokProduct($name) {
        $product = Product::where('name', $name)->get();

        $stockInProduct = StockInProduct::where('product_id', $product->first()->id)
            ->with('produksi')
            ->get()
            ->map(function ($item) {
                return (object)[
                    'tanggal_transaksi' => $item->created_at,
                    'jumlah_barang'     => $item->quantity,
                    'no_bach'           => optional($item->produksi)->batch_number ?? $item->batch_number ?? '-',
                    'tanggal_ed'        => optional($item->produksi)->tanggal_expired ?? $item->expired_date ?? '-',
                    'jenis'             => 'in',
                    'keterangan'        => $item->note ?? 'Stock Masuk',
                    'dokumen_ref'       => '-'
                ];
            });

        $produkKeluar = StockOutProduct::where('product_id', $product->first()->id)
            ->with(['stockInProduct.produksi', 'deliveryItem.delivery'])
            ->get()
            ->map(function ($item) {
                return (object)[
                    'tanggal_transaksi' => $item->created_at,
                    'jumlah_barang'     => $item->quantity,
                    'no_bach'           => optional(optional($item->stockInProduct)->produksi)->batch_number ?? optional($item->stockInProduct)->batch_number ?? '-',
                    'tanggal_ed'        => optional(optional($item->stockInProduct)->produksi)->tanggal_expired ?? optional($item->stockInProduct)->expired_date ?? '-',
                    'jenis'             => 'out',
                    'keterangan'        => $item->note ?? 'Stock Keluar',
                    'dokumen_ref'       => optional(optional($item->deliveryItem)->delivery)->no_sj ?? '-',
                ];
            });

        $transaction = $stockInProduct->concat($produkKeluar)
            ->sortBy(function($item) {
                 $typeOrder = $item->jenis == 'in' ? 0 : 1;
                 return sprintf('%s-%d', $item->tanggal_transaksi->format('Y-m-d H:i:s'), $typeOrder);
            })
            ->values();

        $sisa = 0;

        foreach ($transaction as $tr) {
            if ($tr->jenis === 'in') {
                $sisa += $tr->jumlah_barang;
            }

            if ($tr->jenis === 'out') {
                $sisa -= $tr->jumlah_barang;
            }

            $tr->running_sisa = $sisa;
        }

        $rowsPerPage = 22;
        $chunks = $transaction->chunk($rowsPerPage);

        $pdf = PDF::loadView('print.print-kartu-stok-product', [
            'inventory'     => $product,
            'transaction'   => $transaction,
            'rowsPerPage'   => $rowsPerPage,
            'chunks'        => $chunks
        ]);

        return $pdf->stream('Kartu-Stok-Produk.pdf', [
            'Content-Type' => 'application/pdf'
        ]);
    }

    public function catatanBatchProduksi() {
        $produksi = Produksi::orderBy('tanggal_produksi', 'asc')->get();

        $rowsPerPage = 23;
        $chunks = $produksi->chunk($rowsPerPage);

        $pdf = PDF::loadView('print.print-catatan-batch-produksi', [
            'chunks' => $chunks,
            'rowsPerPage' => $rowsPerPage
        ]);

        return $pdf->stream('Catatan-Batch-Produksi.pdf', [
            'Content-Type' => 'application/pdf'
        ]);
    }

    public function printInventory() {
        $inventory = Inventory::all()->map(function ($item) {

            // total masuk
            $totalIn = StockIn::where('inventory_id', $item->id)->sum('stok');

            // total keluar
            $totalOut = StockOut::where('inventory_id', $item->id)->sum('jumlah');

            // stok tersedia
            $item->stok_tersedia = $totalIn - $totalOut;

            return $item;
        });

        $chunks = $inventory->chunk(53);

        $pdf = PDF::loadView('print.print-inventory', [
            'chunks' => $chunks
        ]);

        return $pdf->stream('Kartu Stock Gudang.pdf', [
            'Content-Type' => 'application/pdf'
        ]);
    }

    public function printProduct() {
        $product = Product::all()->map(function ($item) {

            // total masuk
            $totalIn = StockInProduct::where('product_id', $item->id)->sum('quantity');

            // total keluar
            $totalOut = StockOutProduct::where('product_id', $item->id)->sum('quantity');

            // stok tersedia
            $item->stock_tersedia = $totalIn - $totalOut;

            return $item;
        });

        $chunks = $product->chunk(27);

        $pdf = PDF::loadView('print.print-product-list', [
            'chunks' => $chunks
        ]);

        return $pdf->setPaper('A4', 'landscape')->stream('Daftar Produk.pdf', [
            'Content-Type' => 'application/pdf'
        ]);
    }

    public function printMaterial(Request $request) {
        $tanggal = $request->tanggal ?? Carbon::now()->toDateString();

        $selectedProduksiIds = $request->selected_produksi_ids ?? [];
        $selectedUmumIdentifiers = $request->selected_umum_identifiers ?? [];
        $hasSelections = $request->has('selected_produksi_ids') || $request->has('selected_umum_identifiers');

        $bahanBakuQuery = MaterialProduksi::where(function($q) {
                $q->whereNotNull('formula_bahan_baku_id')
                  ->orWhereHas('inventory', function($q2) {
                      $q2->where('type', 'Bahan Baku');
                  });
            })
            ->with(['produksi.product', 'inventory', 'stockOuts.stockIn', 'user']);

        if ($hasSelections) {
            $bahanBakuQuery->where(function($q) use ($selectedProduksiIds, $selectedUmumIdentifiers) {
                if (!empty($selectedProduksiIds) && !empty($selectedUmumIdentifiers)) {
                    $q->whereIn('produksi_id', $selectedProduksiIds)
                      ->orWhereNull('produksi_id');
                } elseif (!empty($selectedProduksiIds)) {
                    $q->whereIn('produksi_id', $selectedProduksiIds);
                } elseif (!empty($selectedUmumIdentifiers)) {
                    $q->whereNull('produksi_id');
                }
            });
        } else {
            $bahanBakuQuery->where('persetujuan', 'done')
                ->where(function($q) use ($tanggal) {
                    $q->whereDate('created_at', $tanggal)
                      ->orWhereHas('produksi', function($q2) use ($tanggal) {
                          $q2->whereDate('created_at', $tanggal);
                      });
                });
        }
        
        $bahanBakuAll = $bahanBakuQuery->get();

        if ($hasSelections && !empty($selectedProduksiIds) && empty($selectedUmumIdentifiers)) {
            $bahanBaku = $bahanBakuAll->filter(function($item) use ($selectedProduksiIds) {
                return in_array($item->produksi_id, $selectedProduksiIds);
            });
        } elseif ($hasSelections && !empty($selectedUmumIdentifiers)) {
            $bahanBaku = $bahanBakuAll->filter(function($item) use ($selectedProduksiIds, $selectedUmumIdentifiers) {
                if ($item->produksi_id) {
                    return in_array($item->produksi_id, $selectedProduksiIds);
                } else {
                    $identifier = $item->keterangan . '--' . $item->created_at->format('dmyHi');
                    return in_array($identifier, $selectedUmumIdentifiers);
                }
            });
        } else {
            $bahanBaku = $bahanBakuAll;
        }

        $groupedBahanBaku = $this->groupMaterialData($bahanBaku);

        $pdf1 = null;
        if ($groupedBahanBaku->isNotEmpty()) {
            $pdf1 = Pdf::loadView('print.print-request-material', [
                'permintaan_material' => $groupedBahanBaku,
                'tanggal' => $tanggal
            ])->setPaper('A4', 'landscape')->output();
        }

        $bahanKemasQuery = MaterialProduksi::where(function($q) {
                $q->whereNotNull('formula_bahan_kemas_id')
                  ->orWhereHas('inventory', function($q2) {
                      $q2->whereIn('type', ['Bahan Kemas', 'Bahan Kemasan', 'Bahan Kemas Label'])
                        ->orWhere('type', 'LIKE', 'Bahan Kemas%');
                  });
            })
            ->with(['produksi.product', 'inventory', 'stockOuts.stockIn', 'user']);

        if ($hasSelections) {
            $bahanKemasQuery->where(function($q) use ($selectedProduksiIds, $selectedUmumIdentifiers) {
                if (!empty($selectedProduksiIds) && !empty($selectedUmumIdentifiers)) {
                    $q->whereIn('produksi_id', $selectedProduksiIds)
                      ->orWhereNull('produksi_id');
                } elseif (!empty($selectedProduksiIds)) {
                    $q->whereIn('produksi_id', $selectedProduksiIds);
                } elseif (!empty($selectedUmumIdentifiers)) {
                    $q->whereNull('produksi_id');
                }
            });
        } else {
            $bahanKemasQuery->where('persetujuan', 'done')
                ->where(function($q) use ($tanggal) {
                    $q->whereDate('created_at', $tanggal)
                      ->orWhereHas('produksi', function($q2) use ($tanggal) {
                          $q2->whereDate('created_at', $tanggal);
                      });
                });
        }
        
        $bahanKemasAll = $bahanKemasQuery->get();

        if ($hasSelections && !empty($selectedProduksiIds) && empty($selectedUmumIdentifiers)) {
            $bahanKemas = $bahanKemasAll->filter(function($item) use ($selectedProduksiIds) {
                return in_array($item->produksi_id, $selectedProduksiIds);
            });
        } elseif ($hasSelections && !empty($selectedUmumIdentifiers)) {
            $bahanKemas = $bahanKemasAll->filter(function($item) use ($selectedProduksiIds, $selectedUmumIdentifiers) {
                if ($item->produksi_id) {
                    return in_array($item->produksi_id, $selectedProduksiIds);
                } else {
                    $identifier = $item->keterangan . '--' . $item->created_at->format('dmyHi');
                    return in_array($identifier, $selectedUmumIdentifiers);
                }
            });
        } else {
            $bahanKemas = $bahanKemasAll;
        }

        $groupedBahanKemas = $this->groupMaterialData($bahanKemas);

        $pdf2 = null;
        if ($groupedBahanKemas->isNotEmpty()) {
            $pdf2 = Pdf::loadView('print.print-request-material-pelengkap', [
                'permintaan_material' => $groupedBahanKemas,
                'tanggal' => $tanggal
            ])->setPaper('A4', 'landscape')->output();
        }
        // END OF PDF 2

        // START MERGE PDF
        if (isset($pdf1) && isset($pdf2)) {
            $merger = PDFMerger::init();
            $merger->addString($pdf1);
            $merger->addString($pdf2);
            $merger->merge();
            $pdfOutput = $merger->output();
        } elseif (isset($pdf1)) {
            $pdfOutput = $pdf1;
        } elseif (isset($pdf2)) {
            $pdfOutput = $pdf2;
        } else {
            return redirect()->back()->with('error', 'Tidak ada data material untuk dicetak.');
        }
        // END OFF MERGE PDF

        $filename = 'Form Permintaan Material ' . $tanggal . '.pdf';

        return response($pdfOutput, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="'.$filename.'"');
    }

    private function groupMaterialData($materialProduksi) {
        $groupedByProduct = [];

        foreach ($materialProduksi as $item) {
            if ($item->produksi_id) {
                $productId = $item->produksi->product_id;
                $productNameFull = $item->produksi->product->name ?? 'Unknown';

                // potong nomor batch nya dari keterangan.
                if (preg_match('/^.*?\d+\s?(ml|l)\b/i', $item->stockOuts->first()->keterangan ?? '', $matches)) {
                    $keterangan = $matches[0];
                } else {
                    $keterangan = $item->stockOuts->first()->keterangan ?? ''; // Jika tidak ada format mL/L, ambil semua
                }

                preg_match('/\b\d+(?:[\.,]\d+)?\s*(?:ml|l|mg|g|kg)\b/i', $productNameFull, $matches);
                $productSize = $matches[0] ?? '';
                $productName = trim(preg_replace('/\b\d+(?:[\.,]\d+)?\s*(?:ml|l|mg|g|kg)\b/i', '', $productNameFull));


                $batchNumber = $item->produksi->batch_number;
            } else {
                $identifierKey = $item->keterangan . '--' . ($item->created_at ? $item->created_at->format('dmyHi') : '');
                $productId = 'umum_' . md5($identifierKey);
                $productName = '';
                $productSize = '';
                $keterangan = $item->keterangan ?? 'Permintaan Umum';
                $batchNumber = '';
            }

            // data untuk list material
            $materialId = $item->inventory_id;
            $materialName = $item->inventory->name ?? 'Unknown';
            $jumlah = $item->jumlah;
            $requestBy = $item->user->username ?? 'Tio';
            $stockOut = $item->stockOuts->first();
            $takenFrom = $stockOut->user->username ?? '-';


            if (!isset($groupedByProduct[$productId])) {
                $groupedByProduct[$productId] = [
                    'product_name' => $productName,
                    'product_size' => $productSize,
                    'keterangan' => $keterangan,
                    'created_at' => ($item->produksi && $item->produksi->created_at) ? $item->produksi->created_at : $item->created_at,
                    'batches' => [],
                    'materials' => []
                ];
            }

            if (!in_array($batchNumber, $groupedByProduct[$productId]['batches'])) {
                $groupedByProduct[$productId]['batches'][] = $batchNumber;
            }

            if (!isset($groupedByProduct[$productId]['materials'][$materialId])) {
                $groupedByProduct[$productId]['materials'][$materialId] = [
                    'material_name' => $materialName,
                    'request_by' => $requestBy,
                    'taken_from' => $takenFrom,
                    'total_jumlah' => 0,
                    'stock_out_details' => []
                ];
            }

            $groupedByProduct[$productId]['materials'][$materialId]['total_jumlah'] += $jumlah;

            foreach ($item->stockOuts as $stockOut) {
                $bNum = ($stockOut->stockIn && $stockOut->stockIn->nomor_batch) ? $stockOut->stockIn->nomor_batch : '';
                if (!isset($groupedByProduct[$productId]['materials'][$materialId]['stock_out_details'][$bNum])) {
                    $groupedByProduct[$productId]['materials'][$materialId]['stock_out_details'][$bNum] = 0;
                }
                $groupedByProduct[$productId]['materials'][$materialId]['stock_out_details'][$bNum] += $stockOut->jumlah;
            }
        }

        foreach ($groupedByProduct as &$productGroup) {
            $batches = $productGroup['batches'];
            usort($batches, 'strnatcmp');
            if (count($batches) > 1) {
                $productGroup['batch_first'] = $batches[0];
                $productGroup['batch_last'] = end($batches);
                $productGroup['is_batch_range'] = true;
            } else {
                $productGroup['batch_first'] = $batches[0] ?? '-';
                $productGroup['batch_last'] = '';
                $productGroup['is_batch_range'] = false;
            }

            foreach ($productGroup['materials'] as &$material) {
                if (empty($material['stock_out_details'])) {
                    $material['stock_out_details'][''] = 0;
                }
                $material['row_count'] = count($material['stock_out_details']);
            }
        }

        return collect($groupedByProduct);
    }

    public function bukuPenerimaanBarang(Request $request) {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', '300');

        $query = StockIn::with(['inventory', 'suplierMaterial', 'user'])
            ->whereNot('kategori', 'stock_opname');

        if ($request->has('year') && !empty($request->year)) {
            $query->whereYear('tanggal_masuk', $request->year);
        }

        $stockIn = $query->orderBy('tanggal_masuk', 'asc')->get();

        $pdf = Pdf::loadView('print.buku-penerimaan-barang', [
            'stockIn' => $stockIn,
            'year' => $request->year
        ])->setPaper('A4', 'landscape');

        return $pdf->stream('Buku Penerimaan Barang.pdf');
    }

    public function logBookPengirimanBarang(Request $request) {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', '300');

        $deliveries = Deliveries::with([
            'items.order.product',
            'items.order.user', 
            'items.stockOuts.stockInProduct.produksi',
            'invoice'
        ])->orderBy('tanggal_terbit', 'asc')->get();

        $pdf = Pdf::loadView('print.log-book-pengiriman-barang', [
            'deliveries' => $deliveries,
        ])->setPaper('A4', 'landscape');

        return $pdf->stream('Log Book Pengiriman Barang.pdf');
    }

    public function printStabilitas($id) {
        $stabilitas = PemantauanStabilitas::with(['produksi.product'])
            ->findOrFail($id);

        $detail_stabilitas = DetailPemantauanStabilitas::with('hasil')
            ->where('pemantauan_stabilitas_id', $stabilitas->id)
            ->get();
            
        $acuan_quality = QualityControl::where('formula_id', $stabilitas->produksi->formula_bahan_baku_id)->orderBy('urutan')->get();
        $pemeriksaan_qc = QualityControlCheck::where('produksi_id', $stabilitas->produksi_id)->get();

        $pdf = Pdf::loadView('print.print-stabilitas-produk-jadi', [
            'stabilitas' => $stabilitas,
            'detail_stabilitas' => $detail_stabilitas,
            'acuan_quality' => $acuan_quality,
            'pemeriksaan_qc' => $pemeriksaan_qc,
        ])->setPaper('A4', 'landscape');
        
        return $pdf->stream('Stabilitas Produk Jadi.pdf');
    }
}