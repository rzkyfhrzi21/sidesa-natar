<?php
require_once __DIR__ . '/_layout.php';

function templateSuratTidakMampu($data)
{
    $no = $data['nomor_surat'] ?? '-';
    $nama = $data['nama_lengkap'] ?? '-';
    $nik = $data['nik'] ?? '-';
    $alamat = $data['alamat'] ?? '-';
    $keperluan = $data['keperluan'] ?? 'Keperluan administrasi';

    $html = suratHeader($data['desa'] ?? []);
    $html .= '
    <div style="text-align:center;">
        <h3 style="margin:0;">SURAT KETERANGAN TIDAK MAMPU</h3>
        <div>Nomor: <b>' . htmlspecialchars($no) . '</b></div>
    </div>
    <br>
    <div>Yang bertanda tangan di bawah ini menerangkan bahwa:</div><br>
    <table cellpadding="3">
        <tr><td width="30%">Nama</td><td width="5%">:</td><td width="65%"><b>' . htmlspecialchars($nama) . '</b></td></tr>
        <tr><td>NIK</td><td>:</td><td>' . htmlspecialchars($nik) . '</td></tr>
        <tr><td>Alamat</td><td>:</td><td>' . htmlspecialchars($alamat) . '</td></tr>
    </table>
    <br>
    <div>Benar yang bersangkutan tergolong keluarga tidak mampu untuk keperluan: <b>' . htmlspecialchars($keperluan) . '</b>.</div>
    ';

    $html .= suratFooterTTD($data['ttd'] ?? []);
    return $html;
}
