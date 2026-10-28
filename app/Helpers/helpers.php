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