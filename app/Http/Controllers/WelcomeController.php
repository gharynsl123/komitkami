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
     * Dilengkapi perlindungan multi-layer Anti-Spam (Honeypot + Time-Gate + Content Heuristic)
     */
    public function requestDocument(Request $request)
    {
        // =========================================================================
        // 1. ANTI-SPAM LAYER: HONEYPOT CHECK
        // Field 'company_website' disembunyikan dari manusia via CSS.
        // Jika terisi, pengirim 100% adalah robot spammer.
        // =========================================================================
        if ($request->filled('company_website')) {
            Log::warning('Spam bot diblokir via Honeypot trap dari IP: ' . $request->ip());
            return $this->botFakeSuccessResponse($request);
        }

        // =========================================================================
        // 2. ANTI-SPAM LAYER: TIME-GATE CHECK
        // Manusia butuh minimal 2-3 detik untuk membaca dan mengetik form.
        // Jika form dikirim dalam waktu < 2 detik setelah dimuat, pasti robot.
        // =========================================================================
        if ($request->filled('_form_time')) {
            try {
                $formTime = decrypt($request->input('_form_time'));
                $elapsed = time() - $formTime;
                if ($elapsed < 2) {
                    Log::warning("Spam bot diblokir via Time-Gate (waktu submit: {$elapsed} detik) dari IP: " . $request->ip());
                    return $this->botFakeSuccessResponse($request);
                }
            } catch (\Exception $e) {
                Log::warning('Spam bot diblokir karena token waktu tidak valid dari IP: ' . $request->ip());
                return $this->botFakeSuccessResponse($request);
            }
        }

        // =========================================================================
        // 3. ANTI-SPAM LAYER: LINK SPAMMER CONTENT HEURISTIC
        // Spammer bot sering menyisipkan URL/link website di field nama atau instansi.
        // =========================================================================
        $name = (string) $request->input('name', '');
        $company = (string) $request->input('company', '');
        if (preg_match('/(https?:\/\/|www\.|\.ru|\.cn|<a\s|\[url=)/i', $name . ' ' . $company)) {
            Log::warning('Spam bot diblokir karena menyisipkan link promosi di nama/instansi dari IP: ' . $request->ip());
            return $this->botFakeSuccessResponse($request);
        }
        
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

    /**
     * Respon sukses palsu (Silent Discard) untuk bot spam.
     * Bot mengira misinya berhasil sehingga tidak mengulang request,
     * tetapi email admin tidak akan pernah dikirimi spam.
     */
    private function botFakeSuccessResponse(Request $request)
    {
        $message = "Permintaan dokumen berhasil dikirim!";
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'name'    => $request->input('name', 'Pengunjung'),
                'email'   => $request->input('email', ''),
                'doc_type'=> $request->input('doc_type', 'Dokumen'),
            ]);
        }
        return redirect()->back()->with('success_doc_request', $message);
    }
}
