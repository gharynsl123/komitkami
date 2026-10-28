<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Brand;
use App\StockInProduct;
use App\StockOutProduct;
use App\User;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $products = Product::with('brand')->get();

        // Lakukan perhitungan stok untuk setiap produk
        foreach ($products as $product) {
            $stockIn = StockInProduct::where('product_id', $product->id)
                ->sum('quantity');

            $stockOut = StockOutProduct::where('product_id', $product->id)
                ->sum('quantity');

            $product->data_stock = $stockIn - $stockOut;
        }

        $brandOrder = [
            'alkessi',
            'king',
            'frics',
            'deca',
            'diarra',
            'cerplast',
            'chrona care',
            'force-x',
            'ok-med',
            'dispo',
            'medizsisma',
        ];

        $products = $products->sortBy(function ($product) use ($brandOrder) {
            $brandName = strtolower(trim($product->brand->name ?? ''));
            $position = array_search($brandName, $brandOrder);
            return $position === false ? PHP_INT_MAX : $position;
        })->values();

        return view('products.index-product', compact('products'));
    }

    function create()
    {
        $products = Product::all();
        $brand = Brand::all();
        $customers = User::whereHas('roles', function($q) {
            $q->where('role_name', 'Customer');
        })->get();
        return view('products.create-product', compact('brand', 'products', 'customers'));
    }

    function store(Request $request)
    {
        $dataStore = $request->except(['customer_id', 'customer_price']);
        
        // Sanitize price: Remove "Rp ", dots, and change comma to dot
        if (isset($dataStore['price'])) {
            $cleanPrice = str_replace(['Rp ', '.'], '', $dataStore['price']);
            $dataStore['price'] = str_replace(',', '.', $cleanPrice);
        }
        $product = Product::create($dataStore);

        return redirect('/produk')->with('success', 'Product Berhasil di tambahkan');
    }

    function edit($id)
    {
        $product = Product::find($id);
        $brand = Brand::all();
        $customers = User::whereHas('roles', function($q) {
            $q->where('role_name', 'Customer');
        })->get();
        return view('products.edit-product', compact('product', 'brand', 'customers'));
    }


    function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $dataUpdate = $request->except(['customer_id', 'customer_price']);

        if (isset($dataUpdate['price'])) {
            $cleanPrice = str_replace(['Rp ', '.'], '', $dataUpdate['price']);
            $dataUpdate['price'] = str_replace(',', '.', $cleanPrice);
        }

        $product->fill($dataUpdate);
        $product->save();

        return redirect('/produk')->with('success', 'Product Berhasil Di Update');
    }

    public function show($id)
    {
        $product = Product::with('brand', 'brand.users', 'produkjadi')->findOrFail($id);

        $product->sum_in = StockInProduct::where('product_id', $id)
            ->sum('quantity');

        $product->sum_out = StockOutProduct::where('product_id', $id)
            ->sum('quantity');

        $product->stock_akhir = $product->sum_in - $product->sum_out;

        $stockCards = StockInProduct::where('product_id', $product->id)
            ->select('id', 'product_id', 'quantity', 'created_at', 'note', \DB::raw("'in' as type"))
            ->unionAll(StockOutProduct::where('product_id', $product->id)->select('id', 'product_id', 'quantity', 'created_at', 'note', \DB::raw("'out' as type")))
            ->orderBy('created_at', 'asc')
            ->get();
        $runningStock = 0;
        foreach ($stockCards as $card) {
            if ($card->type == 'in') {
                $runningStock += $card->quantity;
            } else {
                $runningStock -= $card->quantity;
            }
            $card->running_stock = $runningStock;
        }
        $product->stockCards = $stockCards;
        return view('products.show-product', compact('product', 'stockCards'));
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);

        $product->delete();
        return redirect('/produk')->with('success', 'Product berhasil di hapus');
    }

    public function storeInManual(Request $request, $id)
    {
        $dataStore = $request->all();

        $dataStore['product_id'] = $id;
        StockInProduct::create($dataStore);
        return redirect()->back()->with('success', 'Stock Berhasil di tambahkan');
    }

}