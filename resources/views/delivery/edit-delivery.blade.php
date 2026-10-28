@extends('layouts.app')
@section('title', 'Komitkami Helper | Detail Pengiriman')
@section('content')
    <div class="container mt-5">
        <div class="row">
            <!-- Left Column: Edit Form -->
            <div class="col-lg-6">
                <div class="card shadow-sm p-3 h-100">
                    <h5 class="card-title fw-bold mb-3">Edit Pengiriman</h5>
                    <form action="{{ route('delivery.update_status', $delivery->slug) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Upload Photo Surat Jalan -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Upload Bukti Surat Jalan</label>
                            <div class="input-group">
                                <input type="file"
                                    class="border-1 border form-control @error('photo_surat_jalan') is-invalid @enderror"
                                    name="photo_surat_jalan" accept="image/*,application/pdf" id="photoInput">
                            </div>
                            @error('photo_surat_jalan')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-2">Format: JPG, PNG, PDF (Max 5MB)</small>
                        </div>

                        <!-- Status Checklist -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Status Pengiriman</label>
                            <div class="border rounded p-3 bg-light">
                                <div class="form-check mb-2">
                                    <input type="radio" class="form-check-input" name="status" value="pending" id="pending"
                                        {{ $delivery->status == 'pending' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pending">
                                        ⏳ Pending
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="radio" class="form-check-input" name="status" value="in_progress"
                                        id="in_progress" {{ $delivery->status == 'in_progress' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="in_progress">
                                        🚚 Dalam Perjalanan
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="radio" class="form-check-input" name="status" value="delivered"
                                        id="delivered" {{ $delivery->status == 'delivered' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="delivered">
                                        ✅ Terkirim (Surat Jalan Kembali)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="status" value="cancelled"
                                        id="cancelled" {{ $delivery->status == 'cancelled' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="cancelled">
                                        ❌ Dibatalkan
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Nomor Invoice -->
                        <div class="mb-4">
                            <label for="no_invoice" class="form-label fw-bold">Nomor Invoice</label>
                            <input type="text"
                                class="form-control border border-1 px-2 py-1 @error('no_invoice') is-invalid @enderror"
                                id="no_invoice" name="no_invoice" placeholder="Contoh: 956343"
                                value="{{ old('no_invoice', $invoices->no_invoice ?? '') }}">
                            @error('no_invoice')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">    
                            <label for="customer_refrence" class="form-label text-muted small">Customer Reference (Opsional)</label>
                            <input type="text" class="form-control border px-3" name="customer_refrence" id="customer_refrence" value="{{ old('customer_reference') }}" placeholder="Contoh: PO-001 - SJ-002">
                        </div>

                        @if($invoices)
                            <!-- invoice -->
                            <div class="mb-4">
                                <label for="status_invoice" class="form-label fw-bold">Status Invoice</label>
                                <select class="form-select @error('status_invoice') is-invalid @enderror px-2 py-1"
                                    id="status_invoice" name="status_invoice">
                                    <option value="un-paid" {{ $invoices->status == 'un-paid' ? 'selected' : '' }}>un paid
                                    </option>
                                    <option value="paid" {{ $invoices->status == 'paid' ? 'selected' : '' }}>paid</option>
                                </select>
                                @error('status_invoice')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <!-- Buttons -->
                        <div class="d-grid gap-2 d-sm-flex justify-content-sm-end mt-4">
                            <a href="{{ route('delivery.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column: Document Preview -->
            <div class="col-lg-6">
                <div class="card shadow-sm p-3 h-100">
                    <h5 class="card-title fw-bold mb-3">Preview Dokumen</h5>
                    <div class="d-flex flex-column justify-content-center align-items-center bg-light border rounded h-100"
                        style="min-height: 400px; position: relative;" id="previewContainer">
                        @php
                            $fileUrl = $delivery->photo_surat_jalan ? asset($delivery->photo_surat_jalan) : null;
                            $ext = $delivery->photo_surat_jalan ? strtolower(pathinfo($delivery->photo_surat_jalan, PATHINFO_EXTENSION)) : null;
                        @endphp

                        @if(!$fileUrl)
                            <div class="text-center text-muted" id="emptyState">
                                <i class="bi bi-file-earmark-image display-4"></i>
                                <p class="mt-2">Belum ada dokumen yang diunggah.</p>
                            </div>
                        @endif

                        <!-- Image Preview -->
                        <img src="{{ in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']) ? $fileUrl : '' }}" class="img-fluid"
                            style="{{ in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']) ? '' : 'display:none;' }} max-height:100%; max-width:100%; object-fit:contain;"
                            id="imgPreview">

                        <!-- PDF Canvas -->
                        <canvas id="pdfCanvas" style="display:none; max-width:100%; border:1px solid #ddd;"></canvas>

                        <!-- Pagination -->
                        <div id="pdfPagination" class="mt-2" style="display:none;">
                            <button class="btn btn-sm btn-secondary" id="prevPage">Prev</button>
                            <span class="mx-2">Page <span id="pageNum">1</span> / <span id="pageCount">1</span></span>
                            <button class="btn btn-sm btn-secondary" id="nextPage">Next</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const photoInput = document.getElementById('photoInput');
            const imgPreview = document.getElementById('imgPreview');
            const emptyState = document.getElementById('emptyState');

            const pdfCanvas = document.getElementById('pdfCanvas');
            const pdfPagination = document.getElementById('pdfPagination');

            const pageNum = document.getElementById('pageNum');
            const pageCount = document.getElementById('pageCount');

            const prevPage = document.getElementById('prevPage');
            const nextPage = document.getElementById('nextPage');

            const existingFile = @json($fileUrl);
            const existingExt = @json($ext);

            let pdfDoc = null;
            let currentPage = 1;

            const ctx = pdfCanvas.getContext('2d');

            function renderPage(num) {

                pdfDoc.getPage(num).then(function (page) {

                    const viewport = page.getViewport({ scale: 1.5 });

                    pdfCanvas.height = viewport.height;
                    pdfCanvas.width = viewport.width;

                    const renderContext = {
                        canvasContext: ctx,
                        viewport: viewport
                    };

                    page.render(renderContext);

                    pageNum.textContent = num;

                });
            }

            if (existingFile && existingExt === 'pdf') {
                const encodedUrl = encodeURI(existingFile);
                pdfjsLib.getDocument(encodedUrl).promise.then(function (pdf) {
                    pdfDoc = pdf;
                    currentPage = 1;
                    pageCount.textContent = pdf.numPages;

                    pdfCanvas.style.display = 'block';
                    pdfPagination.style.display = 'block';
                    imgPreview.style.display = 'none';

                    if (emptyState) emptyState.style.display = 'none';
                    renderPage(currentPage);
                });
            }

            prevPage.addEventListener('click', function () {

                if (currentPage <= 1) return;

                currentPage--;
                renderPage(currentPage);

            });

            nextPage.addEventListener('click', function () {

                if (currentPage >= pdfDoc.numPages) return;

                currentPage++;
                renderPage(currentPage);

            });


            photoInput.addEventListener('change', function (event) {

                const file = event.target.files[0];

                if (!file) return;

                const fileType = file.type;

                const fileReader = new FileReader();

                fileReader.onload = function (e) {

                    if (fileType.includes('image')) {

                        imgPreview.src = e.target.result;
                        imgPreview.style.display = 'block';

                        pdfCanvas.style.display = 'none';
                        pdfPagination.style.display = 'none';

                        if (emptyState) emptyState.style.display = 'none';

                    }

                    else if (fileType.includes('pdf')) {

                        const typedarray = new Uint8Array(e.target.result);

                        pdfjsLib.getDocument(typedarray).promise.then(function (pdf) {

                            pdfDoc = pdf;

                            currentPage = 1;

                            pageCount.textContent = pdf.numPages;

                            pdfCanvas.style.display = 'block';
                            pdfPagination.style.display = 'block';
                            imgPreview.style.display = 'none';

                            if (emptyState) emptyState.style.display = 'none';

                            renderPage(currentPage);

                        });

                    }

                };

                if (fileType.includes('pdf')) {
                    fileReader.readAsArrayBuffer(file);
                } else {
                    fileReader.readAsDataURL(file);
                }

            });

        });
    </script>
@endsection