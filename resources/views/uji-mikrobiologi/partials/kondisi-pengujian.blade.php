{{-- Partial view untuk Clean & Dirty Condition per mikroba --}}
@php
    [$cleanKoloni, $cleanPangkat] = \App\Http\Controllers\MikrobiologiController::extractKoloniPangkat($existing->clean_cfu_ml ?? null);
    [$dirtyKoloni, $dirtyPangkat] = \App\Http\Controllers\MikrobiologiController::extractKoloniPangkat($existing->dirty_cfu_ml ?? null);
@endphp
<div class="row g-3">
    {{-- SUB-SECTION: CLEAN CONDITION --}}
    <div class="col-md-6">
        <div class="card shadow-none h-100" style="border-radius: 14px; border: 1px solid #d1fae5; background: #ffffff;">
            <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center"
                 style="background: #f0fdf4; border-top-left-radius: 13px; border-top-right-radius: 13px; border-bottom: 1px solid #d1fae5;">
                <strong class="text-xs text-dark text-uppercase">
                    <i class="material-icons text-xs text-success me-1 align-middle">cleaning_services</i>
                    A. Clean Condition &mdash; {{ $nama }}
                </strong>
                <span class="badge bg-success bg-opacity-10 text-white text-xxs font-weight-bold" style="border-radius: 6px;">Bersih</span>
            </div>
            <div class="card-body p-3">
                <div class="row g-2 align-items-end mb-3">
                    <div class="col-6">
                        <label class="form-label text-xxs font-weight-bold mb-1 text-dark">Koloni Hidup (Clean)</label>
                        <div class="input-group input-group-modern">
                            <input type="number" step="any" id="clean_koloni_{{ $key }}"
                                   class="form-control text-center input-kondisi"
                                   data-key="{{ $key }}" data-cond="clean"
                                   value="{{ $cleanKoloni }}"
                                   placeholder="Contoh: 1">
                            <span class="input-group-text">Koloni</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-xxs font-weight-bold mb-1 text-dark">Pengenceran (10<sup>x</sup>)</label>
                        <div class="input-group input-group-modern">
                            <input type="number" step="any" id="clean_pangkat_{{ $key }}"
                                   class="form-control text-center input-kondisi"
                                   data-key="{{ $key }}" data-cond="clean"
                                   value="{{ $cleanPangkat }}"
                                   placeholder="Contoh: 1">
                            <span class="input-group-text">10<sup>x</sup></span>
                        </div>
                    </div>
                </div>

                <div class="row g-2 align-items-end mb-3">
                    <div class="col-6">
                        <label class="form-label text-xxs font-weight-bold mb-1 text-secondary">CFU/mL (Clean)</label>
                        <input type="number" step="any" name="mikroba[{{ $key }}][clean_cfu_ml]" id="clean_cfu_{{ $key }}"
                               class="form-control form-control-readonly text-center text-dark"
                               value="{{ $existing->clean_cfu_ml ?? '' }}"
                               placeholder="0" readonly>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-xxs font-weight-bold mb-1 text-secondary">Log (Clean)</label>
                        <input type="number" step="any" name="mikroba[{{ $key }}][clean_log]" id="clean_log_{{ $key }}"
                               class="form-control form-control-readonly text-center text-dark"
                               value="{{ $existing->clean_log ?? '' }}"
                               placeholder="0.00" readonly>
                    </div>
                </div>

                <div>
                    <label class="form-label text-xxs font-weight-bold text-success mb-1">% Mikroba Terbunuh (Clean)</label>
                    <div class="input-group input-group-modern">
                        <input type="number" step="any" name="mikroba[{{ $key }}][persen_clean]" id="clean_persen_{{ $key }}"
                               class="form-control text-center text-success font-weight-bold form-control-readonly"
                               value="{{ $existing->persen_clean ?? '' }}"
                               placeholder="0.00" readonly>
                        <span class="input-group-text font-weight-bold text-success" style="background-color: #ecfdf5; border-color: #a7f3d0;">%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SUB-SECTION: DIRTY CONDITION --}}
    <div class="col-md-6">
        <div class="card shadow-none h-100" style="border-radius: 14px; border: 1px solid #fef3c7; background: #ffffff;">
            <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center"
                 style="background: #fffbeb; border-top-left-radius: 13px; border-top-right-radius: 13px; border-bottom: 1px solid #fef3c7;">
                <strong class="text-xs text-dark text-uppercase">
                    <i class="material-icons text-xs text-warning me-1 align-middle">dirty_lens</i>
                    B. Dirty Condition &mdash; {{ $nama }}
                </strong>
                <span class="badge bg-warning bg-opacity-10 text-white text-xxs font-weight-bold" style="border-radius: 6px;">Kotor</span>
            </div>
            <div class="card-body p-3">
                <div class="row g-2 align-items-end mb-3">
                    <div class="col-6">
                        <label class="form-label text-xxs font-weight-bold mb-1 text-dark">Koloni Hidup (Dirty)</label>
                        <div class="input-group input-group-modern">
                            <input type="number" step="any" id="dirty_koloni_{{ $key }}"
                                   class="form-control text-center input-kondisi"
                                   data-key="{{ $key }}" data-cond="dirty"
                                   value="{{ $dirtyKoloni }}"
                                   placeholder="Contoh: 4">
                            <span class="input-group-text">Koloni</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-xxs font-weight-bold mb-1 text-dark">Pengenceran (10<sup>x</sup>)</label>
                        <div class="input-group input-group-modern">
                            <input type="number" step="any" id="dirty_pangkat_{{ $key }}"
                                   class="form-control text-center input-kondisi"
                                   data-key="{{ $key }}" data-cond="dirty"
                                   value="{{ $dirtyPangkat }}"
                                   placeholder="Contoh: 1">
                            <span class="input-group-text">10<sup>x</sup></span>
                        </div>
                    </div>
                </div>

                <div class="row g-2 align-items-end mb-3">
                    <div class="col-6">
                        <label class="form-label text-xxs font-weight-bold mb-1 text-secondary">CFU/mL (Dirty)</label>
                        <input type="number" step="any" name="mikroba[{{ $key }}][dirty_cfu_ml]" id="dirty_cfu_{{ $key }}"
                               class="form-control form-control-readonly text-center text-dark"
                               value="{{ $existing->dirty_cfu_ml ?? '' }}"
                               placeholder="0" readonly>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-xxs font-weight-bold mb-1 text-secondary">Log (Dirty)</label>
                        <input type="number" step="any" name="mikroba[{{ $key }}][dirty_log]" id="dirty_log_{{ $key }}"
                               class="form-control form-control-readonly text-center text-dark"
                               value="{{ $existing->dirty_log ?? '' }}"
                               placeholder="0.00" readonly>
                    </div>
                </div>

                <div>
                    <label class="form-label text-xxs font-weight-bold text-warning mb-1">% Mikroba Terbunuh (Dirty)</label>
                    <div class="input-group input-group-modern">
                        <input type="number" step="any" name="mikroba[{{ $key }}][persen_dirty]" id="dirty_persen_{{ $key }}"
                               class="form-control text-center text-warning font-weight-bold form-control-readonly"
                               value="{{ $existing->persen_dirty ?? '' }}"
                               placeholder="0.00" readonly>
                        <span class="input-group-text font-weight-bold text-warning" style="background-color: #fffbeb; border-color: #fde68a;">%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
