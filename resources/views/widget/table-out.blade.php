<div class="row">
    <div class="col-12">
        <div class="card px-4">
            <div class="card-body px-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table table-borderless table-hover border border-0 align-items-center mb-0"
                        id="dataTableDefault">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    penanggung jawab
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                    Informasi Barang</th>
                                <th
                                    class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Nomor Batch pengambilan</th>
                                <th
                                    class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Keterangan Untuk</th>
                                <th
                                    class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Jumlah Barang Keluar</th>
                                <th class="text-secondary opacity-7"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stockOut as $items)
                            <tr>
                                <td>
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-sm">
                                            {{ $items->user ? $items->user->name : 'no user' }}</h6>
                                        <p class="text-xs text-secondary mb-0">
                                            {{ $items->tanggal_keluar ? $items->tanggal_keluar->format('d-m-Y') : 'none' }}
                                        </p>
                                    </div>
                                </td>
                                <td>
                                    <h6 class="text-xs font-weight-bold mb-0">{{$items->inventory->name}}</h6>
                                    <p class="text-xs text-secondary mb-0">{{$items->inventory->code}}</p>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    <h6 class="mb-0 text-sm">{{ $items->stockIn->nomor_batch ?? 'Tidak Ada' }}</h6>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    <h6 class="mb-0 text-sm">
                                        {{ $items->keterangan ?? ($items->permintaanMaterial?->produksi?->batch_number ?? 'none') }}
                                    </h6>
                                </td>
                                <td class="align-middle text-center">
                                    <span class="text-secondary text-xs font-weight-bold">
                                        {{formatStokInventory($items->jumlah)}}
                                    </span>
                                </td>
                                <!-- tombol delete -->
                                <td class="align-middle">
                                    <form action="{{ url('/delete-out/'.$items->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-secondary btn font-weight-bold text-xs delete-link"
                                            data-toggle="tooltip" data-original-title="Delete item"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')">
                                            <i class="material-icons">delete</i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>