<div class="card p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Spesifikasi Parameter Uji Material : {{ $material->name ?? '' }}</h4>
    </div>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        @if(isset($manufacturers) && $manufacturers->count() > 0)
            @foreach($manufacturers as $index => $mfg)
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $index == 0 ? 'active' : '' }}" id="tab-{{ $mfg->id }}" data-bs-toggle="tab"
                        data-bs-target="#content-{{ $mfg->id }}" type="button" role="tab" aria-controls="content-{{ $mfg->id }}"
                        aria-selected="{{ $index == 0 ? 'true' : 'false' }}">
                        <span class="manufacturer-name" data-id="{{ $mfg->id }}" data-name="{{ $mfg->name }}"
                            data-address="{{ $mfg->address }}" title="Double click to edit">{{ $mfg->name }}</span>
                        <span class="ms-2 text-danger cursor-pointer delete-manufacturer" data-id="{{ $mfg->id }}"
                            data-name="{{ $mfg->name }}" style="cursor: pointer; font-weight: bold; font-size: 1.2rem;"
                            title="Hapus">&times;</span>
                    </button>
                </li>
            @endforeach
        @endif
        <li class="nav-item" role="presentation">
            <button class="nav-link m-0 text-primary fw-bold" type="button" data-bs-toggle="modal"
                data-bs-target="#addManufacturerModal">
                + Tambah Produsen
            </button>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content mt-4" id="myTabContent">
        @if(!isset($manufacturers) || $manufacturers->count() == 0)
            <div class="alert alert-info">Belum ada produsen. Silakan tambah produsen terlebih dahulu.</div>
        @else
            @foreach($manufacturers as $index => $mfg)
                @php
                    $mfgSpecs = $specsByManufacturer->get($mfg->id) ?? collect();
                    $getValue = function ($section, $param) use ($mfgSpecs) {
                        $spec = $mfgSpecs->where('section_name', $section)->where('parameter', $param)->first();
                        return $spec ? $spec->spesifikasi : '';
                    };
                @endphp
                <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" id="content-{{ $mfg->id }}" role="tabpanel"
                    aria-labelledby="tab-{{ $mfg->id }}">
                    <form action="{{ url('/inventory/spesifikasi/' . $material->slug . '/' . $mfg->id) }}" method="POST">
                        @csrf
                        <div style="background-color: #F3F3F3; border-radius:12px; padding:12px;">
                            <div class="row">
                                @if($material->type != 'Bahan Baku')
                                    <div class="col-md-12">
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <h5 class="mb-0 fw-bold">Parameter Uji Bahan Kemas</h5>
                                            <button type="button" class="btn btn-sm btn-primary btn-add-parameter m-0" data-mfg="{{ $mfg->id }}">+ Tambah Parameter</button>
                                        </div>
                                        <div class="row parameter-container-{{ $mfg->id }}">
                                            @php
                                                $savedKemasParams = $mfgSpecs->where('section_name', 'Bahan Kemas');
                                            @endphp
                                            @if($savedKemasParams->count() > 0)
                                                @foreach($savedKemasParams as $spec)
                                                    <div class="col-md-6 mb-3 parameter-row">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <input type="hidden" name="section_name[]" value="Bahan Kemas">
                                                            <input type="text" name="parameter[]" value="{{ $spec->parameter }}" class="form-control border px-2 py-1" placeholder="Nama Parameter" required>
                                                            <input type="text" name="spesifikasi[]" value="{{ $spec->spesifikasi }}" class="form-control border px-2 py-1" placeholder="Spesifikasi (kosongkan untuk hapus)">
                                                            <button type="button" class="btn btn-sm btn-danger btn-remove-parameter m-0 d-inline-flex align-items-center justify-content-center">
                                                                <i class="material-icons" style="font-size: 18px;">delete</i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="col-md-6 mb-3 parameter-row">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <input type="hidden" name="section_name[]" value="Bahan Kemas">
                                                        <input type="text" name="parameter[]" value="" class="form-control border px-2 py-1" placeholder="Nama Parameter" required>
                                                        <input type="text" name="spesifikasi[]" value="" class="form-control border px-2 py-1" placeholder="Spesifikasi (kosongkan untuk hapus)">
                                                        <button type="button" class="btn btn-sm btn-danger btn-remove-parameter m-0 d-inline-flex align-items-center justify-content-center">
                                                            <i class="material-icons" style="font-size: 18px;">delete</i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <!-- Organolepis -->
                                    <div class="col-md-6">
                                        <h5 class="mb-4 fw-bold">Organolepis</h5>
                                        @php $parameters = ['Bentuk', 'Warna', 'Rasa']; @endphp
                                        @foreach ($parameters as $parameter)
                                            <div class="d-flex align-items-center gap-3 mb-4">
                                                <p class="m-0 w-25">{{ $parameter }}</p>
                                                <input type="hidden" name="section_name[]" value="Organolepis">
                                                <input type="hidden" name="parameter[]" value="{{ $parameter }}">
                                                <input type="text" name="spesifikasi[]"
                                                    value="{{ $getValue('Organolepis', $parameter) }}"
                                                    class="form-control border px-2 py-1">
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- pH -->
                                    <div class="col-md-6">
                                        <h5 class="mb-4 fw-bold">pH</h5>
                                        @php $parameters = ['Spesifikasi', 'Suhu']; @endphp
                                        @foreach ($parameters as $parameter)
                                            <div class="d-flex align-items-center gap-3 mb-4">
                                                <p class="m-0 w-25">{{ $parameter }}</p>
                                                <input type="hidden" name="section_name[]" value="pH">
                                                <input type="hidden" name="parameter[]" value="{{ $parameter }}">
                                                <input type="text" name="spesifikasi[]" value="{{ $getValue('pH', $parameter) }}"
                                                    class="form-control border px-2 py-1">
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Viscosity -->
                                    <div class="col-md-6">
                                        <h5 class="mb-4 fw-bold">Viscosity</h5>
                                        @php $parameters = ['Spesifikasi', 'Spindle', 'Speed', 'Suhu']; @endphp
                                        @foreach ($parameters as $parameter)
                                            <div class="d-flex align-items-center gap-3 mb-4">
                                                <p class="m-0 w-25">{{ $parameter }}</p>
                                                <input type="hidden" name="section_name[]" value="Viscosity">
                                                <input type="hidden" name="parameter[]" value="{{ $parameter }}">
                                                <input type="text" name="spesifikasi[]" value="{{ $getValue('Viscosity', $parameter) }}"
                                                    class="form-control border px-2 py-1">
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Density -->
                                    <div class="col-md-6">
                                        <h5 class="mb-4 fw-bold">Density</h5>
                                        @php $parameters = ['Spesifikasi', 'Suhu']; @endphp
                                        @foreach ($parameters as $parameter)
                                            <div class="d-flex align-items-center gap-3 mb-4">
                                                <p class="m-0 w-25">{{ $parameter }}</p>
                                                <input type="hidden" name="section_name[]" value="Density">
                                                <input type="hidden" name="parameter[]" value="{{ $parameter }}">
                                                <input type="text" name="spesifikasi[]" value="{{ $getValue('Density', $parameter) }}"
                                                    class="form-control border px-2 py-1">
                                            </div>
                                        @endforeach
                                        <h5 class="mb-4 fw-bold">Kadar Alkohol</h5>
                                        <div class="d-flex align-items-center gap-3 mb-4">
                                            <p class="m-0 w-25">Spesifikasi</p>
                                            <input type="hidden" name="section_name[]" value="Kadar Alkohol">
                                            <input type="hidden" name="parameter[]" value="Spesifikasi">
                                            <input type="text" name="spesifikasi[]"
                                                value="{{ $getValue('Kadar Alkohol', 'Spesifikasi') }}"
                                                class="form-control border px-2 py-1">
                                        </div>

                                    </div>

                                    <!-- Microbiological -->
                                    <div class="col-md-6">
                                        <h5 class="mb-4 fw-bold">Microbiological</h5>
                                        @php $parameters = ['Bacteri', 'Yeast & Molds']; @endphp
                                        @foreach ($parameters as $parameter)
                                            <div class="d-flex align-items-center gap-3 mb-4">
                                                <p class="m-0 w-25">{{ $parameter }}</p>
                                                <input type="hidden" name="section_name[]" value="Microbiological">
                                                <input type="hidden" name="parameter[]" value="{{ $parameter }}">
                                                <input type="text" name="spesifikasi[]"
                                                    value="{{ $getValue('Microbiological', $parameter) }}"
                                                    class="form-control border px-2 py-1">
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Specific Gravity -->
                                    <div class="col-md-6">
                                        <h5 class="mb-4 fw-bold">Specific Gravity</h5>
                                        @php $parameters = ['Spesifikasi', 'Suhu']; @endphp
                                        @foreach ($parameters as $parameter)
                                            <div class="d-flex align-items-center gap-3 mb-4">
                                                <p class="m-0 w-25">{{ $parameter }}</p>
                                                <input type="hidden" name="section_name[]" value="Specific Gravity">
                                                <input type="hidden" name="parameter[]" value="{{ $parameter }}">
                                                <input type="text" name="spesifikasi[]"
                                                    value="{{ $getValue('Specific Gravity', $parameter) }}"
                                                    class="form-control border px-2 py-1">
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn mb-0 btn-success">Simpan Spesifikasi</button>
                            <a href="{{ url('/local-inventory') }}" class="btn mb-0 btn-secondary">Kembali</a>
                        </div>
                    </form>
                </div>
            @endforeach
        @endif
    </div>
</div>

<!-- Modal Add Manufacturer -->
<div class="modal fade" id="addManufacturerModal" tabindex="-1" aria-labelledby="addManufacturerModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ url('/inventory/spesifikasi/' . ($material->slug ?? '') . '/manufacturer') }}"
                method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addManufacturerModalLabel">Tambah Produsen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Produsen</label>
                        <input type="text" class="form-control border px-2 py-1 mb-2" name="name" required>

                        <label class="form-label fw-bold">Alamat Produsen</label>
                        <input type="text" class="form-control border px-2 py-1" name="address" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-add-parameter')) {
                const btn = e.target.closest('.btn-add-parameter');
                const mfgId = btn.getAttribute('data-mfg');
                const container = document.querySelector('.parameter-container-' + mfgId);
                if (container) {
                    const rowHtml = `
                        <div class="col-md-6 mb-3 parameter-row">
                            <div class="d-flex align-items-center gap-2">
                                <input type="hidden" name="section_name[]" value="Bahan Kemas">
                                <input type="text" name="parameter[]" value="" class="form-control border px-2 py-1" placeholder="Nama Parameter" required>
                                <input type="text" name="spesifikasi[]" value="" class="form-control border px-2 py-1" placeholder="Spesifikasi (kosongkan untuk hapus)">
                                <button type="button" class="btn btn-sm btn-danger btn-remove-parameter m-0 d-inline-flex align-items-center justify-content-center">
                                    <i class="material-icons" style="font-size: 18px;">delete</i>
                                </button>
                            </div>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', rowHtml);
                }
            }
            if (e.target.closest('.btn-remove-parameter')) {
                const btn = e.target.closest('.btn-remove-parameter');
                const row = btn.closest('.parameter-row');
                row.style.display = 'none';
                row.querySelector('input[name="spesifikasi[]"]').value = '';
                row.querySelector('input[name="parameter[]"]').removeAttribute('required');
            }
        });
    });
</script>
