@extends('layouts.app')
@section('content')
<div class="d-flex gap-2 align-items-center mb-3">
    <a href="{{url()->previous()}}" class="btn m-0 btn-secondary btn-sm">Kembali</a>
    <h5 class="m-0">Mulai Proses Produksi By Group</h5>
</div>

<div class="row">
    <div class="col-md-8 mb-3">

        @forelse($produksi as $items)
        <div class="card p-3 mb-2 produksi-item" data-id="{{ $items->id }}" data-name="{{ $items->product->name }}" data-batch="{{ $items->batch_number }}" data-size="{{ $items->batch_size }}">
            <div class="row">
                <div class="col-md-4">
                        {{$items->product->name}} <br>
                        {{$items->batch_number}} 
                </div>
                <div class="col-md-4 align-items-center d-flex justify-content-center">{{$items->batch_size}}</div>
                <div class="col-md-4 align-items-center d-flex justify-content-end">
                    <button type="button" class="btn btn-success gap-2 btn-sm m-0 btn-add">
                        <i class="material-icons">check</i>
                        <span>Add</span>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="card p-3">
            <p class="m-0">Tidak ada produksi yang sedang dalam proses konfirmasi.</p>
        </div>
        @endforelse

    </div>

    <div class="col-md-4">
        <div class="card p-3">
            <h6>Preview</h6>
            <p class="text-muted">Mulai proses produksi untuk beberapa batch sekaligus.</p>
            <form method="POST">
                @csrf
                @method('PUT')
                <div id="list-preview-produksi">
                    <!-- List Preview Akan Muncul Disini -->
                </div>
                <!-- tambahkan onlick confirmation "ada yakin ingin memproses batch "08123, 81273, 91278"" -->
                <button type="submit" id="btn-start" disabled class="btn btn-primary w-100 mt-3">Mulai Produksi</button>
            </form>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {

    const previewContainer = document.getElementById("list-preview-produksi");
    const btnStart = document.getElementById("btn-start");
    const form = previewContainer.closest("form");
    let selectedIds = [];

    document.querySelectorAll(".btn-add").forEach(function(button) {
        button.addEventListener("click", function() {

            let parentCard = this.closest(".produksi-item");
            let id = parentCard.dataset.id;
            let name = parentCard.dataset.name;
            let batch = parentCard.dataset.batch;
            let size = parentCard.dataset.size;

            // Cek apakah item sudah ditambahkan
            if (document.getElementById("preview-item-" + id)) {
                alert("Batch ini sudah ditambahkan.");
                return;
            }

            // Tambahkan ke array ID
            selectedIds.push(id);

            // Buat element baru untuk preview
            let row = document.createElement("div");
            row.classList.add("border", "rounded", "p-2", "mb-2", "bg-light", "d-flex", "justify-content-between", "align-items-center");
            row.id = "preview-item-" + id;
            row.innerHTML = `
                <div>
                    <strong>${name}</strong> <br>
                    Batch: ${batch} | Size: ${size}
                </div>
                <button type="button" class="btn btn-danger btn-sm btn-remove" data-id="${id}">
                    Hapus
                </button>
                <input type="hidden" name="produksi_id[]" value="${id}">
            `;

            previewContainer.appendChild(row);

            // Disable tombol Add setelah ditambahkan
            this.disabled = true;
            this.classList.replace("btn-success", "btn-secondary");
            this.innerHTML = `<i class="material-icons">check</i> Added`;

            // Event tombol Hapus
            row.querySelector(".btn-remove").addEventListener("click", () => {

                // Hapus elemen preview
                row.remove();

                // Aktifkan kembali tombol add
                button.disabled = false;
                button.classList.replace("btn-secondary", "btn-success");
                button.innerHTML = `<i class="material-icons">check</i> Add`;

                // Hapus dari array selectedIds
                selectedIds = selectedIds.filter(val => val !== id);

                updateButtonState();
            });

            updateButtonState();
        });
    });

    function updateButtonState() {
        // pastikan event tidak terdaftar berkali-kali

        if (selectedIds.length > 0) {

            btnStart.removeAttribute("disabled");

            // generate route action
            const url = "/produksi-group-start/" + selectedIds.join('-');
            form.action = url;

            btnStart.addEventListener('click', function(e){
                e.preventDefault();

                Swal.fire({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin memproses produksi batch: " + selectedIds.join(', ') + " ?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya, proses!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // submit form, bukan redirect
                    }
                });
            });

        } else {

            btnStart.setAttribute("disabled", true);
            form.removeAttribute("action");

        }
    }

});
</script>
@endsection
