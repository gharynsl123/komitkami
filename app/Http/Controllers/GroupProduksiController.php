<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Produksi;

class GroupProduksiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function prosesByGroup()
    {
        $produksi = Produksi::where('status', 'confirm')->get();
        return view('produksi.proses-by-group.index-proses-by-group', compact('produksi'));
    }

    public function startProduksi($ids) {
        $arrayIds = explode('-', $ids);
        // kasih wadah untuk group by produksi seperti table baru untuk menaping id produksi tersebut.
        

        Produksi::whereIn('id', $arrayIds)->update([
            'status' => 'on-going',
            'updated_at' => now(),
        ]);

        return redirect()->route('produksi.group.mulai', $ids)
            ->with('success', 'Proses produksi telah dimulai untuk batch yang dipilih.');
    }

    public function mulaiProses($ids)
    {
        $arrayIds = explode('-', $ids);
        $produksi = Produksi::whereIn('id', $arrayIds)->get();

        return view('produksi.proses-by-group.start-proses-produksi', compact('produksi', 'ids'));
    }

}
