@extends('layouts.app')
@section('title', 'Komitkami Helper | Log Sistem')
@section('content')

<div class="container-fluid mb-5">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800" style="font-weight: 700;">Log Aktivitas Sistem</h1>
            <p class="text-muted mb-0" style="font-size: 14px;">Pantau seluruh riwayat perubahan dan aktivitas pengguna di dalam sistem.</p>
        </div>
        <div class="mt-3 mt-md-0">
            <button class="btn btn-outline-primary btn-sm shadow-sm" onclick="location.reload()" style="border-radius: 8px;">
                <i class="fas fa-sync-alt"></i> Refresh Data
            </button>
        </div>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow-sm border-0" style="border-radius: 12px;">
        <div class="card-header bg-white border-bottom py-3 d-flex flex-row align-items-center justify-content-between" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
            <h6 class="m-0" style="font-weight: 600; color: #4e73df;">Tabel Riwayat Aktivitas</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle" id="dataTableDefault" width="100%" cellspacing="0">
                    <thead style="background-color: #f8f9fc; color: #5a5c69;">
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th width="25%">Informasi Pengguna</th>
                            <th class="text-center" width="15%">Aksi</th>
                            <th width="40%">Keterangan Detail</th>
                            <th class="text-center" width="15%">Waktu Kejadian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($log->reverse() as $index => $items)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div style="width: 38px; height: 38px; background: linear-gradient(135deg, #4e73df, #224abe); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 16px; margin-right: 12px;">
                                        {{ strtoupper(substr($items->users->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; color: #333;">{{ $items->users->name ?? 'Pengguna Dihapus' }}</div>
                                        <div style="font-size: 12px; color: #888;">ID User: {{ $items->users_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                @php
                                    $actionClass = 'bg-secondary';
                                    $actionText = strtolower($items->action);
                                    if(strpos($actionText, 'create') !== false || strpos($actionText, 'tambah') !== false || strpos($actionText, 'insert') !== false) {
                                        $actionClass = 'bg-success text-white';
                                    } elseif(strpos($actionText, 'update') !== false || strpos($actionText, 'edit') !== false || strpos($actionText, 'ubah') !== false) {
                                        $actionClass = 'bg-warning text-dark';
                                    } elseif(strpos($actionText, 'delete') !== false || strpos($actionText, 'hapus') !== false) {
                                        $actionClass = 'bg-danger text-white';
                                    } elseif(strpos($actionText, 'login') !== false || strpos($actionText, 'masuk') !== false) {
                                        $actionClass = 'bg-info text-white';
                                    } else {
                                        $actionClass = 'bg-primary text-white';
                                    }
                                @endphp
                                <span class="badge {{ $actionClass }}" style="padding: 6px 10px; border-radius: 6px; font-weight: 600; letter-spacing: 0.5px;">
                                    {{ strtoupper($items->action) }}
                                </span>
                            </td>
                            <td>
                                <span style="color: #555; font-size: 14px;">{{ $items->keterangan }}</span>
                            </td>
                            <td class="text-center">
                                <div style="font-weight: 600; color: #444; font-size: 13px;">
                                    {{ \Carbon\Carbon::parse($items->created_at)->format('d M Y') }}
                                </div>
                                <div style="font-size: 12px; color: #888;">
                                    {{ \Carbon\Carbon::parse($items->created_at)->format('H:i:s') }}
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center" style="padding: 30px; color: #888;">
                                <i class="fas fa-folder-open mb-2" style="font-size: 24px; color: #ccc;"></i>
                                <br>Belum ada data log aktivitas di sistem
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection