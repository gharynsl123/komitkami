<?php

use App\Http\Controllers\HomeController;
use App\Order;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Exports\MonthlyPurchasesExport;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!

|
*/

Auth::routes(['register' => false]);

Route::get('lang/{locale}', function ($locale) {
    Session::put('locale', $locale);
    return redirect()->back();
});

// log admin
Route::get('/log-table', 'HomeController@logWeb');

Route::get('/download-excel', function () {
    $monthlyPurchases = [];

    $months = [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December'
    ];

    $productNames = Order::select('product.name')
        ->join('product', 'order.product_id', '=', 'product.id')
        ->where('order.id_user', Auth::user()->id)
        ->groupBy('product.name')
        ->pluck('product.name');

    foreach ($productNames as $productName) {
        $monthlyPurchases[$productName] = [];
        foreach ($months as $month) {
            $quantity = Order::selectRaw('SUM(order.quantity) AS total_quantity')
                ->join('product', 'order.product_id', '=', 'product.id')
                ->where('order.id_user', Auth::user()->id)
                ->where('product.name', $productName)
                ->whereMonth('order.created_at', Carbon::parse($month)->month)
                ->value('total_quantity');

            $monthlyPurchases[$productName][$month] = $quantity ?: 0;
        }
    }

    return Excel::download(new MonthlyPurchasesExport($monthlyPurchases, $months), 'Pembelian Barang Report.xlsx');
})->name('download.excel');


Route::get('/fetch-purchases-by-year', 'HomeController@fetchPurchasesByYear');
Route::get('/fetch-admin-income-by-year', 'HomeController@fetchAdminIncomeByYear');

// User Route
Route::post('/store-user', 'UserController@store');
Route::get('/create-user', 'UserController@create');
Route::get('/edit-profile/{id}', 'UserController@editProfile');
Route::get('/edit-user/{id}', 'UserController@edit');
Route::put('/update-user/{id}', 'UserController@update');
Route::get('/user-configuration', 'UserController@index');
Route::get('/profile-user', 'UserController@profile');
Route::get('/detail-user/{id}', 'UserController@detail');
Route::delete('/delete-user/{id}', 'UserController@delete');

// Role Route
Route::get('/role-configuration', 'RoleController@index');
Route::post('/add-role', 'RoleController@store');

// Dashboar Route
Route::get('/', 'WelcomeController@landingPage');
Route::get('/profile-perusahaan', 'WelcomeController@companyProfile');
Route::get('/dashboard', 'HomeController@index');
Route::get('/preview-data/charts', 'HomeController@showChartData');

// Transaction Route
Route::get('/transaction/in', 'TransactionController@index');
Route::get('/transaction/out', 'TransactionController@out');
Route::post('/store-transaction', 'TransactionController@store');
Route::post('/request-store-out', 'TransactionController@requestStoreOut');
Route::get('/get-batch-details', 'TransactionController@getBatchDetails');
Route::delete('/delete-out/{id}', 'TransactionController@deleteOut');
Route::delete('/delete-in/{id}', 'TransactionController@deleteIn');
Route::get('/get-supliers/{id}', 'TransactionController@getSupliers');



// Ordering Route
Route::get('/view-order', 'OrderController@index');
Route::get('/po-selesai', 'OrderController@filterSelesai');
Route::get('/buat-order', 'OrderController@create');
Route::post('/store-order', 'OrderController@store');
Route::post('/revisi-order', 'OrderController@kirimRevisi');
Route::put('/reject-order/{id}', 'OrderController@reject');
Route::put('/accept-order/{id}', 'OrderController@accept');
Route::put('/change-time/{id}', 'OrderController@changetime');
Route::get('/order-details/{slug}', 'OrderController@detail');
Route::patch('/update-status/{id}', 'OrderController@updateStatus')->name('update-status');

// guess access
Route::get('/our-products', 'WelcomeController@ourProducts');

Route::prefix('our-products')->group(function () {
    Route::view('/liquid-for-reprocessing-cssd', 'guest.our-products.liquid')->name('products.liquid');
    Route::view('/antiseptic-medical-grade', 'guest.our-products.antiseptic')->name('products.antiseptic');
    Route::view('/detergent-for-laundry', 'guest.our-products.detergent_laundry')->name('products.laundry');
    Route::view('/alkohol-70-80-96-98', 'guest.our-products.alkohol')->name('products.alkohol');
    Route::view('/reagen-patologi-anatomi', 'guest.our-products.reagen')->name('products.reagen');
    Route::view('/detergent-disinfetan-kitchen', 'guest.our-products.detergent_kitchen')->name('products.kitchen');
    Route::view('/detergent-disinfektan-kitchen', 'guest.our-products.detergent_kitchen');
    Route::view('/ultrasound-transmission-gel', 'guest.our-products.ultrasound_gel')->name('products.ultrasound');

    Route::get('/detail/{slug}', 'WelcomeController@productDetail')->name('product.detail');
});

// Direct alias for product detail
Route::get('/detail/{slug}', 'WelcomeController@productDetail')->name('product.detail.short');
Route::get('/product/{slug}', 'WelcomeController@productDetail');
Route::post('/request-document', 'WelcomeController@requestDocument')->name('product.requestDocument')->middleware('throttle:10,1');

// Inventory Route
Route::get('/local-inventory', 'InventoryController@index');
Route::get('/local-inventory/detail/{slug}', 'InventoryController@detail');
Route::post('/inventory/spesifikasi/{slug}/manufacturer', 'InventoryController@storeManufacturer');
Route::put('/inventory/spesifikasi/{slug}/manufacturer/{manufacturer_id}', 'InventoryController@updateManufacturer');
Route::post('/inventory/spesifikasi/{slug}/{manufacturer_id}', 'InventoryController@storeSpesifikasi');
Route::delete('/inventory/spesifikasi/{slug}/{manufacturer_id}', 'InventoryController@deleteSpesifikasi');
Route::get('/buat-bahan-baku', 'InventoryController@create');
Route::post('/store-bahanbaku', 'InventoryController@store');
Route::get('/edit-material/{slug}', 'InventoryController@edit');
// delete material
Route::delete('/delete-material/{id}', 'InventoryController@delete');
// update data material
Route::put('/update-material/{slug}', 'InventoryController@update');

// Stock Opname (baru)
Route::get('/stock-opname', 'StockOpnameController@index')->name('stock-opname.index');
Route::get('/stock-opname/get-batches/{type}/{id}', 'StockOpnameController@getBatches')->name('stock-opname.batches');
Route::post('/stock-opname', 'StockOpnameController@store')->name('stock-opname.store');
Route::get('/stock-opname/history', 'StockOpnameController@history')->name('stock-opname.history');
Route::post('/stock-opname/print', 'StockOpnameController@printReport')->name('stock-opname.print');

// Form QC
Route::post('/store-sample-ipc', 'QualityController@storeSampleIPC');
Route::post('/store-inspeksi-proses', 'QualityController@storeInspeksiProses');
Route::post('/store-report-ipc', 'QualityController@storeReportIPC');
Route::post('/store-sample-produk-jadi', 'QualityController@storeSampleProdukJadi');
Route::post('/store-pengujian-produk', 'QualityController@storePengujianProduk');

Route::get('/pantau-stabilitas/{id}', 'QualityController@viewPantauStabilitas');
Route::post('/store-pantau-stabilitas', 'QualityController@storePantauStabilitas');
Route::put('/update-pantau-stabilitas/{id}', 'QualityController@updatePantauStabilitas');

Route::get('/print-stabilitas-produk-jadi/{id}', 'PrintController@printStabilitas');

Route::get('/download-spesifikasi', 'QualityController@downloadSpesifikasi')->name('download.spesifikasi');
Route::get('/download-spesifikasi/product/{id}', 'QualityController@downloadPdfProduct')->name('download.spesifikasi.product');
Route::get('/download-spesifikasi/inventory/{inventory_id}/{manufacturer_id}', 'QualityController@downloadPdfInventory')->name('download.spesifikasi.inventory');


// form edit qc
Route::put('/update-sample-ipc/{id}', 'QualityController@updateSampleIPC');
Route::put('/update-inspeksi-proses/{id}', 'QualityController@updateInspeksiProses');
Route::put('/update-report-ipc/{id}', 'QualityController@updateReportIPC');
Route::put('/update-sample-produk-jadi/{id}', 'QualityController@updateSampleProdukJadi');
Route::put('/update-pengujian-produk/{id}', 'QualityController@updatePengujianProduk');


// printRoute
route::get('/view-print-po/{slug}', 'PrintController@preorder')->name('po.print');
Route::get('/riwayat-alat/print', 'PrintController@printRiwayatAlat')->name('riwayat.print');
Route::get('/print-rekap-po', 'PrintController@rekaporder')->name('rekap.print');
Route::get('/print-unpaid-po', 'PrintController@unpaid')->name('unpaid.print');
Route::get('/print-laporan-produksi/{batchnumber}', 'PrintController@laporanProduksi');
Route::get('/print-sertifikat-qc/{batchnumber}', 'PrintController@serfifikatQc');
Route::get('/print-laporan-ipc/{batchnumber}', 'PrintController@laporanIpc');
Route::get('/print-kartu-stok/{slug}', 'PrintController@kartuStok');
Route::get('/print-kartu-stok-product/{name}', 'PrintController@kartuStokProduct');
Route::get('/print-catatan-batch-produksi', 'PrintController@catatanBatchProduksi');
Route::get('/print-stock-gudang', 'PrintController@printInventory');
Route::get('/print-product', 'PrintController@printProduct');
Route::get('/print-buku-penerimaan-barang', 'PrintController@bukuPenerimaanBarang');
Route::get('/print-log-book-product', 'PrintController@logBookPengirimanBarang');

// Produksi By Group
Route::get('/mulai-produksi-by-group', 'GroupProduksiController@prosesByGroup');
Route::put('/produksi-group-start/{ids}', 'GroupProduksiController@startProduksi')->name('produksi.group.start');
Route::get('/mulai-mengerjakan-produksi/{ids}', 'GroupProduksiController@mulaiProses')->name('produksi.group.mulai');

// PO
Route::get('/mutasi', 'OrderController@rekapPO')->name('rekap.po');
Route::get('/detail-rekap-po', 'OrderController@detailRekap')->name('rekap.detail');
Route::get('/pembayaran', 'OrderController@unpaid');
Route::get('/detail-charts', 'OrderController@detailCharts')->name('detailCharts');

// Export Produksi Excel
Route::get('/export-produksi', 'ProduksiController@exportExcel')->name('export.produksi');

// Produksi Route
Route::get('/ruang-produksi', 'ProduksiController@index');
Route::get('view-form-sample-IPC/{batch_number}', 'ProduksiController@viewFormSampleIPC');
Route::get('/buat-tikect-produksi', 'ProduksiController@create');
Route::get('/qc-produksi', 'ProduksiController@indexQC');
Route::get('pengambilan-sample-ipc', 'ProduksiController@sampleIPC');
Route::put('/update-event/{id}', 'ProduksiController@updateEvent');
Route::post('/update-inProses/{id}', 'ProduksiController@updateInProses');
Route::post('/update-sendQty/{id}', 'ProduksiController@updateSendQty');

// edit-data-proses-produksi
Route::put('/update-data-kebersihan', 'ProsesProduksiController@updateKebersihanData');
Route::put('/update-data-penimbangan', 'ProsesProduksiController@updatePenimbanganData');
Route::put('/update-data-proses', 'ProsesProduksiController@updateProsesData');
Route::put('/update-data-rekonsilasi', 'ProsesProduksiController@updateRekonsiliasiData');
Route::put('/update-quality-control', 'ProsesProduksiController@updateDataQc');

Route::get('/get-last-batch-number', 'ProduksiController@getLastProductionNumber');
Route::get('/get-products/{invoice_id}', 'ProduksiController@getProducts');
Route::post('/store-data-tiket', 'ProduksiController@store');
Route::post('/store-data-tiket-khusus', 'ProduksiController@storeKhusus');
Route::get('/get-po-items/{po_id}', 'ProduksiController@getPoItems');
Route::get('/detail-tiket/{date}', 'ProduksiController@show')->name('detail.tiket');
Route::get('/detail-produksi/{batch_number}', 'ProduksiController@detail')->name('detail.produksi');
Route::put('/confirm-produksi/{id}', 'ProduksiController@confirmProduksi');
Route::delete('/delete-tiket-produksi', 'ProduksiController@deleteProduksiTiket')->name('produksi.delete');

Route::get('/formula-templates/{productId}', 'ProduksiController@getFormulaTemplatesByProduct');
Route::get('/formula-templates-kemas/{productId}', 'ProduksiController@getBahanKemasByTemplate');
Route::get('/formula-by-template', 'ProduksiController@getFormulaByTemplate');
Route::get('/formula-kemasan-by-template', 'ProduksiController@getKemasanByTemplate');

Route::delete('/delete/tahapan-proses/{id}', 'ProsesProduksiController@deleteTahapanProses')->name('tahapanProses.delete');
Route::delete('/delete/formula-proses/{id}', 'ProsesProduksiController@formulaDelete')->name('formulaSet.delete');
Route::delete('/delete/quality-control/{id}', 'ProsesProduksiController@deleteQualityControl')->name('qualityControl.delete');
Route::delete('/delete/produk-jadi/{id}', 'ProsesProduksiController@deleteProdukJadi')->name('produkJadi.delete');
Route::put('/update-proses/{id}', 'ProsesProduksiController@updateNamaProses');

Route::get('/mulai-produksi/{batch_number}', 'RuangProduksiController@proses');
Route::get('/riwayat-produksi', 'RuangProduksiController@showRiwayatProduksi');
Route::get('/riwayat-alat-produksi', 'RuangProduksiController@showRiwayatAlatProduksi');
Route::put('/produk-jadi-update/{id}', 'FormulaController@updateProdukJadi');

// edit quality control
Route::get('/atur-ruangan', 'RuangProduksiController@index');
Route::post('/store-nama-ruangan', 'RuangProduksiController@createRuangan');
Route::put('/update-nama-ruangan/{id}', 'RuangProduksiController@update');
Route::put('/kirim-barang-jadi/{id}', 'ProduksiController@kirimBarangJadi')->name('kirim-barang-jadi');
Route::put('/update-barang-jadi/{id}', 'ProduksiController@updateBarangJadi')->name('update-barang-jadi');
Route::delete('/delete-bagian/{id}', 'RuangProduksiController@deleteItem');

Route::post('/store-bagian', 'ProsesProduksiController@storeItems');
Route::post('/store-data-proses', 'ProsesProduksiController@storeProses');
Route::post('/store-quality-control', 'ProsesProduksiController@storeQuality');
Route::post('/store-pemeriksaan-qc', 'ProsesProduksiController@storeQcCheck');
Route::post('/store-acuan-produk-jadi', 'ProsesProduksiController@storeProdukJadi');
Route::post('/store-penimbangan', 'ProsesProduksiController@storePenimbangan');
Route::post('/store-periksaan-ruangan', 'ProsesProduksiController@storeProduksi');

// verifikasi per proses
Route::put('/update-status-kebersihan/{id}', 'ProsesProduksiController@updateStatusKebersihan');
Route::put('/update-status-penimbangan/{id}', 'ProsesProduksiController@updateStatusPenimbangan');
Route::put('/update-status-tahapan/{id}', 'ProsesProduksiController@updateStatusTahapan');
Route::put('/update-status-quality/{id}', 'ProsesProduksiController@updateStatusQuality');
Route::put('/update-status-rekonsiliasi/{id}', 'ProsesProduksiController@updateStatusRekonsiliasi');
Route::get('/get-supervisor-users', 'ProsesProduksiController@getSupervisorUsers');
Route::post('/rekonsiliasa-bahan-kemas', 'ProsesProduksiController@storeRekonsiliasi');
Route::post('/store-quality-control-check', 'ProsesProduksiController@storeCheckQuality');

Route::get('/permintaan-material', 'RequestMaterialController@index');
Route::get('/proses-permintaan/{type}/{identifier}', 'RequestMaterialController@proses');
Route::get('/detail-permintaan/{type}/{identifier}', 'RequestMaterialController@detail');
Route::get('/show-all/permintaan', 'RequestMaterialController@showAll');
Route::post('/store-penyerahan', 'RequestMaterialController@storePenyerahan');
Route::get('/cetak-laporan-permintaan', 'RequestMaterialController@RequestMaterialPreview');

Route::get('/get-formulas-by-batch/{batch_number}', 'TransactionController@getFormulasByBatch');

Route::get('/print-request-material', 'PrintController@printMaterial')->name('print.material');

// ready develerry
Route::get('/pengiriman-barang', 'DeliveryController@index')->name('delivery.index');
Route::get('/buat-devilery', 'DeliveryController@create');
Route::post('/store-ready-order', 'DeliveryController@store')->name('delivery.store');
Route::post('/add-to-list', 'DeliveryController@addToList')->name('delivery.add_to_list');
Route::get('/lihat-surat/{slug}', 'DeliveryController@letter');
Route::get('/print-invoice-sales/{slug}', 'DeliveryController@printInvoice');
Route::get('/buat-invoice/{slug}', 'DeliveryController@updateDelivery');
Route::put('/update-delivery/{slug}', 'DeliveryController@updateStatus')->name('delivery.update_status');
Route::delete('/delivery-item/{id}', 'DeliveryController@destroyItem')->name('delivery.destroy_item');
Route::delete('/delivery/{id}', 'DeliveryController@destroyDelivery')->name('delivery.destroy');

// Manual Invoice
Route::get('/buat-invoice-manual', 'DeliveryController@createManualInvoice')->name('manual-invoice.create');
Route::post('/store-invoice-manual', 'DeliveryController@storeManualInvoice')->name('manual-invoice.store');
Route::get('/print-invoice-manual/{id}', 'DeliveryController@printManualInvoice')->name('manual-invoice.print');
Route::get('/edit-invoice-manual/{id}', 'DeliveryController@editManualInvoice')->name('manual-invoice.edit');
Route::put('/update-invoice-manual/{id}', 'DeliveryController@updateManualInvoice')->name('manual-invoice.update');

// Products Route
Route::get('/produk', 'ProductController@index');
Route::get('/create-products', 'ProductController@create');
Route::get('/edit-products/{id}', 'ProductController@edit');
Route::post('/store-products', 'ProductController@store');
Route::put('/update-product/{id}', 'ProductController@update');
Route::delete('/delete-product/{id}', 'ProductController@delete')->name('products.delete');
Route::get('/products/{id}', 'ProductController@show')->name('products.show');

// Stock In Product Route
Route::post('/store-stock-in-product/{id}', 'ProductController@storeInManual')->name('stock-in-product.manual');

// pengecekan-barang-masuk
Route::get('/pengecekan-barang-masuk', 'PengecekanMaterialController@index');
Route::post('/store-manual-report', 'PengecekanMaterialController@storeManual');
Route::get('/detail-pengecekan-material/{id}', 'PengecekanMaterialController@detail');
Route::get('/cetak-pengecekan-material/{id}', 'PengecekanMaterialController@printReport')->name('pengecekan-material.print');

Route::post('/store-pengambilan-sampel', 'PengecekanMaterialController@storePengambilanSampel');
Route::put('/update-pengambilan-sampel/{id}', 'PengecekanMaterialController@updatePengambilanSampel');

Route::post('/store-inspeksi-penerimaan', 'PengecekanMaterialController@storeInspeksiPenerimaan');
Route::put('/update-inspeksi-penerimaan/{id}', 'PengecekanMaterialController@updateInspeksiPenerimaan');

Route::post('/store-pengujian', 'PengecekanMaterialController@storePengujian');
Route::put('/update-pengujian/{id}', 'PengecekanMaterialController@updatePengujian');

Route::post('/store-laporan-inspeksi', 'PengecekanMaterialController@storeLaporanInspeksi');
Route::put('/update-laporan-inspeksi/{id}', 'PengecekanMaterialController@updateLaporanInspeksi');

// Merek Route
Route::get('/merek', 'MerekController@index');
Route::post('/store-merek', 'MerekController@store');
Route::get('/edit-merek/{id}', 'MerekController@edit')->name('edit-merek');
Route::put('/update-merek/{id}', 'MerekController@update')->name('update-merek');

// Formula Route
Route::get('/formula-produk', 'FormulaController@index');
Route::get('/craete/formula-produk', 'FormulaController@create');
Route::get('/edit-formula/{id}', 'FormulaController@edit');
Route::put('/update-formula/{id}', 'FormulaController@update');
Route::get('/buat-formula-baru', 'FormulaController@create');

// Data Formula Store
Route::delete('/delete-formula/{id}', 'FormulaController@delete')->name('formula.delete');
Route::post('/store-formula', 'FormulaController@store');
Route::post('/store-tahapan-proses/{formula_id}', 'FormulaController@storeTahapanProses');
Route::post('/store-quality-control/{formula_id}', 'FormulaController@storeQualityControl');
Route::post('/store-acuan-laporan-ipc/{formula_id}', 'FormulaController@storeAcuanLaporanIpc');
Route::post('/store-tahapan', 'ProsesProduksiController@storeTahapan');

// Archive
Route::get('/archive', 'ArchiveController@index');

// Laporan Route
Route::get('/laporan-pesanan/{slug}', 'LaporanController@view');
Route::PUT('/confirm-laporan/{id}', 'LaporanController@confirm');
Route::get('/confirm-revisi/order/{slug}', 'LaporanController@confirmRevision');
Route::put('/orders/{id}', 'LaporanController@updateConfirmation')->name('orders.update');Route::get('/test-products', function() { $product = \App\Product::limit(5)->get(); foreach ($product as $p) { $p->price = $p->getPriceForUser(1); } return $product; });


// uji-mikrobiologi
Route::get('/uji-mikrobiologi', 'MikrobiologiController@index');
Route::post('/uji-mikrobiologi/store', 'MikrobiologiController@store')->name('mikrobiologi.store');
Route::get('/proses-uji-mikroba/{batch_number}', 'MikrobiologiController@proses')->name('mikrobiologi.proses');
Route::get('/print-laporan-mikrobiologi/{batch_number}', 'MikrobiologiController@printLaporan')->name('mikrobiologi.print');
Route::get('/print-laporan-mikrobiologi-manual/{id}', 'MikrobiologiController@printLaporanManual')->name('mikrobiologi.print-manual');
