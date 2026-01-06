<?php
require_once __DIR__ . '/_layout.php';

function templateSuratUsaha($data)
{
    $no = $data['nomor_surat'] ?? '-';
    $nama = $data['nama_lengkap'] ?? '-';
    $nik = $data['nik'] ?? '-';
    $alamat = $data['alamat'] ?? '-';
    $namaUsaha = $data['nama_usaha'] ?? '-';
    $alamatUsaha = $data['alamat_usaha'] ?? $alamat;

    $html = suratHeader($data['desa'] ?? []);
    $html .= '
    <div style="text-align:center;">
        <h3 style="margin:0;">SURAT KETERANGAN USAHA</h3>
        <div>Nomor: <b>' . htmlspecialchars($no) . '</b></div>
    </div>
    <br>
    <div>Yang bertanda tangan di bawah ini menerangkan bahwa:</div><br>
    <table cellpadding="3">
        <tr><td width="30%">Nama</td><td width="5%">:</td><td width="65%"><b>' . htmlspecialchars($nama) . '</b></td></tr>
        <tr><td>NIK</td><td>:</td><td>' . htmlspecialchars($nik) . '</td></tr>
        <tr><td>Alamat</td><td>:</td><td>' . htmlspecialchars($alamat) . '</td></tr>
        <tr><td>Nama Usaha</td><td>:</td><td><b>' . htmlspecialchars($namaUsaha) . '</b></td></tr>
        <tr><td>Alamat Usaha</td><td>:</td><td>' . htmlspecialchars($alamatUsaha) . '</td></tr>
    </table>
    <br>
    <div>Benar yang bersangkutan memiliki usaha tersebut di wilayah desa.</div>
    ';

    $html .= suratFooterTTD($data['ttd'] ?? []);
    return $html;
}
