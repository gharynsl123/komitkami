<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Image;

class WelcomeController extends Controller
{
    public function landingPage() {
        return view('guest.beranda');
    }

    public function companyProfile() {
        $images = Image::all();
        return view('guest.profile-company', compact('images'));
    }

    public function ourProducts() {
        return view('guest.our-products');
    }

    public function productDetail($slug) {
        // Path file JSON
        $path = resource_path('data/detail-products.json');

        // Baca isi file JSON
        $json = file_get_contents($path);
        $products = json_decode($json, true);

        // Cari produk berdasarkan slug
        $product = collect($products)->firstWhere('slug', $slug);

        // Kalau gak ketemu, tampilkan error 404
        if (!$product) {
            abort(404, 'Produk tidak ditemukan.');
        }

        // Kirim data produk ke view
        return view('guest.detail-products', compact('product'));
    }


}
