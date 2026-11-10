<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Image;
use App\Mail\DocumentRequestMail;

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

    /**
     * Menangani permintaan dokumen SDS / PDS dari pengunjung website
     * dan meneruskannya ke email admin: persolna1243@gmail.com
     */
    public function requestDocument(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'phone'         => 'nullable|string|max:50',
            'company'       => 'nullable|string|max:255',
            'notes'         => 'nullable|string|max:1000',
            'doc_type'      => 'required|string|max:100',
            'product_title' => 'required|string|max:255',
        ]);

        $adminEmail = 'persolna1243@gmail.com';

        try {
            Mail::to($adminEmail)->send(new DocumentRequestMail($validated));
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email permintaan dokumen ke admin: ' . $e->getMessage());
        }

        $message = "Permintaan dokumen {$validated['doc_type']} berhasil dikirim! Tim admin kami akan segera mengirimkan file PDF ke email {$validated['email']}.";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'name'    => $validated['name'],
                'email'   => $validated['email'],
                'doc_type'=> $validated['doc_type'],
            ]);
        }

        return redirect()->back()->with('success_doc_request', $message);
    }
}
