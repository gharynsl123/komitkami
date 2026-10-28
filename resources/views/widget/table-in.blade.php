<div class="row">
    <div class="col-md-12">
        <div class="card p-3">
            <div class="table-responsive p-0">
                <table class="table table-borderless table-hover border border-0 align-items-center mb-0"
                    id="dataTableDefault">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                Sumber</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                Informasi Barang</th>
                            <th
                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                Jumlah Barang Masuk</th>
                            <th
                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                Nomor Invoice Bahan Baku</th>
                            <th
                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                Nomor Batch Bahan Baku</th>
                            <th class="text-secondary opacity-7"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stockIn->reverse() as $item)
                        <tr>
                            <td>
                                <div class="d-flex flex-column justify-content-center">
                                    <h6 class="mb-0 text-sm">{{$item->vendor}}</h6>
                                    <p class="text-xs text-secondary mb-0">
                                        {{ $item->tanggal_masuk ? $item->tanggal_masuk->format('d-m-Y') : 'none' }}
                                    </p>
                                </div>
                            </td>
                            <td>
                                <h6 class="text-xs font-weight-bold mb-0">{{$item->inventory->name}}</h6>
                                <p class="text-xs text-secondary mb-0">{{$item->inventory->code}}</p>
                            </td>
                            <td class="align-middle text-center">
                                <span class="text-secondary text-xs font-weight-bold">
                                    {{formatStokInventory($item->stok)}}
                                </span>
                            </td>
                            <td class="align-middle text-center">
                                <span class="text-secondary text-xs font-weight-bold">{{$item->nomor_sj ?? 'none'}}</span>
                            </td>
                            <td class="align-middle text-center">
                                <span class="text-secondary text-xs font-weight-bold">{{$item->nomor_batch ?? 'none'}}</span>
                            </td>
                            <!-- action buttons -->
                            <td class="align-middle">
                                <div class="d-flex justify-content-center align-items-center">
                                    <!-- button detail -->
                                    <button type="button" class="btn btn-info font-weight-bold text-xs me-3 mt-3"
                                        data-bs-toggle="modal" data-bs-target="#detailModal{{ $item->id }}" title="Lihat Detail">
                                        <i class="material-icons text-white">info</i>
                                    </button>

                                    <!-- tombol delete -->
                                    <form action="{{ url('/delete-in/'.$item->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-secondary btn font-weight-bold btn-danger text-white text-xs delete-link mt-3"
                                            data-toggle="tooltip" data-original-title="Delete item"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')">
                                            <i class="material-icons">delete</i>
                                        </button>
                                    </form>
                                </div>

                                <!-- Modal Detail -->
                                <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $item->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-light">
                                                <h6 class="modal-title" id="detailModalLabel{{ $item->id }}">Detail Transaksi Stock In</h6>
                                                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <div class="row g-3">
                                                    @php
                                                        $details = [
                                                            'Nama Penyerah' => $item->nama_penyerah ?: 'Tidak ada',
                                                            'Nama Penerima' => $item->user?->name ?: 'Tidak ada',
                                                            'Nama Material' => $item->inventory->name ?: 'Tidak ada',
                                                            'Kode Material' => $item->inventory->code ?: 'Tidak ada',
                                                            'Nama Supplier' => $item->suplierMaterial?->name ?: ($item->vendor ?: 'Tidak ada'),
                                                            'Negara Supplier' => $item->suplierMaterial?->address ?: 'Tidak ada',
                                                            'Nama Produsen' => $item->produsen?->name ?: 'Tidak ada',
                                                            'Negara Produsen' => $item->produsen?->address ?: 'Tidak ada',
                                                            'Nomor Batch' => $item->nomor_batch ?: 'Tidak ada',
                                                            'Jumlah Masuk' => $item->stok ? formatStokInventory($item->stok) . ($item->inventory->type === 'Bahan Baku' ? ' gram' : ' pcs') : 0,
                                                            'Tanggal Masuk' => $item->tanggal_masuk ? \Carbon\Carbon::parse($item->tanggal_masuk)->format('d F Y') : 'Tidak ada',
                                                            'Tanggal Expired' => $item->tanggal_ed ? \Carbon\Carbon::parse($item->tanggal_ed)->format('d F Y') : 'Tidak ada',
                                                            'Keterangan' => $item->keterangan ?: 'Tidak ada',
                                                        ];
                                                    @endphp

                                                    @foreach ($details as $label => $value)
                                                        <div class="col-md-6">
                                                            <div class="detail-card">
                                                                <div class="detail-label">
                                                                    {{ $label }}
                                                                </div>

                                                                <div class="detail-value">
                                                                    {{ $value }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach

                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary btn-sm mb-0" data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Modal -->
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>