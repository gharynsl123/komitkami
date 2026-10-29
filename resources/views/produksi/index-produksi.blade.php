@extends('layouts.app')
@section('title', 'Komitkami Helper | Jadwal Produksi')
@section('content')
<style>
#calendar {
    max-height: 32rem;
}

.external-event {
    margin: 10px 0;
    padding: 5px;
    background-color: #007bff;
    color: white;
    border-radius: 8px;
    cursor: pointer;
}
</style>
<!-- role check -->

<div class="pb-0">
    <div class="d-flex justify-content-end gap-2 align-items-center">
        @if(auth()->user()->hasAnyRole(['Production Manager', 'Admin', 'Inventory Manager']))
        @if(!auth()->user()->hasRole('Inventory Manager'))
        <a href="{{ url('/buat-tikect-produksi') }}" class="btn m-0 btn-primary">Buat Tiket</a>
        @endif
        <a href="{{ url('/export-produksi') }}" class="btn m-0 btn-secondary">Download Produksi Excel</a>
        @endif
    </div>
</div>
<div id="external-events" class="card mt-4 p-3 @if(!auth()->user()->hasAnyRole(['Production Manager', 'Admin'])) d-none @endif">
    <div class="row">
        @forelse($tiketsjadwal as $tiket)
        @php
            $statusClass = match(strtolower($tiket->status)) {
                'pending' => 'bg-info text-white',
                'confirm', 'ongoing' => 'bg-success text-white',
                'cancel' => 'bg-warning text-dark',
                'close', 'done' => 'bg-secondary text-white',
                default => 'bg-light'
            };
        @endphp
        <div class="col-md-6 mb-2">
            <div class="external-event p-2 rounded {{ $statusClass }}" data-id="{{ $tiket->id }}">
                {{ $tiket->batch_number }} - {{ $tiket->product->name }}
            </div>
        </div>
        @empty
        <p class="text-muted m-0">Semua tiket sudah dijadwalkan.</p>
        @endforelse
    </div>
</div>

<div class="mt-4 card p-3">
    <div id="calendar"></div>
</div>

<div class="mt-3 d-flex ">
    <div class="row gap-2">
        <div class="col-auto d-flex align-items-center">
            <span style="display:inline-block;width:24px;height:24px;background:#198754;border-radius:4px;margin-right:8px;"></span>
            <small>Tiket Sedang Berjalan Atau Confirm</small>
        </div>
        <div class="col-auto d-flex align-items-center">
            <span style="display:inline-block;width:24px;height:24px;background:#FF2407FF;border-radius:4px;margin-right:8px;"></span>
            <small>Tiket Gagal Atau Cancel</small>
        </div>
        <div class="col-auto d-flex align-items-center">
            <span style="display:inline-block;width:24px;height:24px;background:#0d6efd;border-radius:4px;margin-right:8px;"></span>
            <small>Tiket Pending Atau Belum Dijadwalkan</small>
        </div>
        <div class="col-auto d-flex align-items-center">
            <span style="display:inline-block;width:24px;height:24px;background:#FFE016FF;border-radius:4px;margin-right:8px;border:1px solid #ccc;"></span>
            <small>Tiket Selesai Pengechekan/Input Barang Jadi</small>
        </div>
        <div class="col-auto d-flex align-items-center">
            <span style="display:inline-block;width:24px;height:24px;background:#6c757d;border-radius:4px;margin-right:8px;"></span>
            <small>Tiket Close</small>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enable dragging for external events
    var containerEl = document.getElementById('external-events');
    new FullCalendar.Draggable(containerEl, {
        itemSelector: '.external-event',
        eventData: function(eventEl) {
            return {
                title: eventEl.innerText,
                id: eventEl.dataset.id,
            };
        },
    });

    // Initialize FullCalendar
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        timeZone: 'Asia/Jakarta',
        editable: true,
        droppable: true,
        eventDisplay: 'block',
        events: [
            @foreach($tikets as $tiket)
            {
                id: '{{ $tiket->id }}',
                title: '{{ $tiket->batch_number }}',
                start: '{{ $tiket->tanggal_produksi ? $tiket->tanggal_produksi->format('Y-m-d') : ""}}',
                product_name: '{{ $tiket->product->name ?? "Unknown" }}',
                batch_size: '{{ $tiket->batch_size }}',
                color: '{{ match(strtolower($tiket->status)) {
                    'pending' => '#0d6efd',     // biru
                    'confirm', 'on-going' => '#198754', // hijau
                    'failed' => '#FF2407FF',      // kuning
                    'done' => '#FFE016FF',
                    'close'=> '#6c757d', // abu
                } }}',
            },
            @endforeach
        ],
        dateClick: function(info) {
            var selectedDate = info.dateStr;
            window.location.href = `/detail-tiket/${selectedDate}`;
        },
        eventClick: function(info) {
            const props = info.event.extendedProps;
            const eventDate = new Date(info.event.start);
            const today = new Date();

            const isToday = eventDate.toDateString() === today.toDateString();

            Swal.fire({
                title: 'Tiket Popup',
                html: `
                    <strong>Jadwal Produksi:</strong> ${eventDate.toLocaleDateString('id-ID')}${isToday ? ' (Hari ini)' : ''}<br>
                    <strong>Nomor Batch:</strong> ${info.event.title}<br>
                    <strong>Nama Produk:</strong> ${props.product_name}<br>
                    <strong>Batch Size:</strong> ${props.batch_size}

                    <div class="d-flex justify-content-center mt-3">
                        <button class="btn btn-primary mt-2" onclick="window.location.href='/detail-produksi/${info.event.title}'">Detail Tiket</button>
                        <button class="btn btn-secondary mt-2 ms-2" onclick="Swal.close()">Tutup</button>
                    </div>
                `,
                icon: 'info',
                showCloseButton: false,
                showCancelButton: false,
                showConfirmButton: false,
            });
        },
        drop: function(info) {
            var eventId = info.draggedEl.dataset.id;
            var newStart = new Date(info.dateStr).toISOString().split('T')[
            0]; // Pastikan dalam format ISO

            fetch(`/update-event/${eventId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        tanggal_produksi: newStart
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Update Successful:', data);
                    info.draggedEl.remove();
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        },
        eventDrop: function(info) {
            var eventId = info.event.id;
            var newStart = info.event.start.toISOString().split('T')[0];

            // Update the event in the database
            fetch(`/update-event/${eventId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        tanggal_produksi: newStart
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Event Moved Successfully:', data);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        },
    });

    calendar.render();
});
</script>
@endsection