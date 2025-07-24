<?php

if (!function_exists('formatRupiah')) {
    function formatRupiah($angka)
    {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }
}

if (!function_exists('cleanRupiah')) {
    function cleanRupiah($rupiah)
    {
        return (int) str_replace(['Rp', '.', ' '], '', $rupiah);
    }
}

if (!function_exists('formatTanggal')) {
    function formatTanggal($tanggal)
    {
        $bulan = [
            1 => 'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];

        $explode = explode('-', date('Y-m-d', strtotime($tanggal)));
        return $explode[2] . ' ' . $bulan[(int)$explode[1]] . ' ' . $explode[0];
    }
}
