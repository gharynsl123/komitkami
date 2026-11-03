<?php

function smartRound($value) {
   $decimal = 0;
    if (strpos($value, '.') !== false) {
        $decimal = strlen(substr(strrchr($value, "."), 1));
    }

    // Format angka dengan jumlah desimal yang sama
    return number_format((float)$value, $decimal, '.', '');
}

function formatJumlah($angka) {
    // Replace titik (.) jadi titik desimal PHP yang bener
    $angka = str_replace('.', '.', $angka);

    // Pecah antara integer dan decimal
    $parts = explode('.', $angka);

    // Kalau ga ada decimal
    if (count($parts) == 1 || $parts[1] == '000') {
        return $parts[0]; // cuma tampilkan integer
    }

    // Kalau ada decimal → tampilkan pakai koma
    return $parts[0] . ',' . $parts[1];
}

function formatStokInventory($val) {
    if ($val === null || $val === '') return '0';
    $num = (float)str_replace(',', '.', (string)$val);
    if (fmod($num, 1) == 0.0) {
        return number_format($num, 0, '.', '');
    }
    return rtrim(rtrim(number_format($num, 3, '.', ''), '0'), '.');
}

function getStepStatus($data, $checkFields = ['hasil', 'dibersihkan_oleh']) {
    if ($data->isEmpty()) return 'status-grey';

    $adaKosong = $data->contains(function ($item) use ($checkFields) {
        foreach ($checkFields as $field) {
            if (empty($item->$field) || $item->$field == 0) return true;
        }
        return false;
    });

    $verified = $data->first()?->status == 'Diverifikasi';

    if (!$verified && !$adaKosong) return 'status-yellow';
    if (!$verified && $adaKosong) return 'status-blue';
    if ($verified && $adaKosong) return 'status-dark-yellow';
    if ($verified && !$adaKosong) return 'status-green';

    return 'status-grey';
}

if (!function_exists('public_asset')) {
    /**
     * Get the optimized source for an asset for PDF viewers or print views.
     * Encodes local public files into Base64 data URIs so that PDF engines (DomPDF)
     * and browser print viewers load images instantly without external HTTP requests
     * or local file access restrictions.
     *
     * @param string|null $path
     * @return string
     */
    function public_asset($path) {
        if (empty($path)) {
            return '';
        }

        // Return immediately if it is already a base64 data URI or full remote URL
        if (str_starts_with($path, 'data:') || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $cleanPath = ltrim($path, '/');
        if (str_starts_with($cleanPath, 'public/')) {
            $cleanPath = substr($cleanPath, 7);
        }

        // Search candidates including standard public_path and common cPanel hosting structures
        $candidates = [
            public_path($cleanPath),
            base_path('../public_html/' . $cleanPath),
            base_path('public_html/' . $cleanPath),
            base_path('public/' . $cleanPath),
            $path,
        ];

        $fullPath = null;
        foreach ($candidates as $candidate) {
            if (!empty($candidate) && file_exists($candidate) && is_file($candidate)) {
                $fullPath = $candidate;
                break;
            }
        }

        if ($fullPath) {
            $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
            $mimeMap = [
                'png'  => 'image/png',
                'jpg'  => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'gif'  => 'image/gif',
                'svg'  => 'image/svg+xml',
                'webp' => 'image/webp',
                'ico'  => 'image/x-icon',
            ];

            $mime = $mimeMap[$extension] ?? (function_exists('mime_content_type') ? @mime_content_type($fullPath) : ('image/' . $extension));
            $data = @file_get_contents($fullPath);

            if ($data !== false) {
                return 'data:' . $mime . ';base64,' . base64_encode($data);
            }
        }

        return public_path($cleanPath);
    }
}
