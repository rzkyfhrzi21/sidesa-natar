<?php
require_once __DIR__ . '/_layout.php';

function templateSuratKematian($data)
{
    $no = $data['nomor_surat'] ?? '-';
    $nama = $data['nama_almarhum'] ?? '-';
    $nik  = $data['nik_almarhum'] ?? '-';
    $tgl  = $data['tanggal_meninggal'] ?? '-';
    $tempat = $data['tempat_meninggal'] ?? '-';
    $sebab = $data['sebab_meninggal'] ?? '-';

    $html = suratHeader($data['desa'] ?? []);
    $html .= '
    <div style="text-align:center;">
        <h3 style="margin:0;">SURAT KETERANGAN KEMATIAN</h3>
        <div>Nomor: <b>' . htmlspecialchars($no) . '</b></div>
    </div>
    <br>
    <table cellpadding="3">
        <tr><td width="30%">Nama</td><td width="5%">:</td><td width="65%"><b>' . htmlspecialchars($nama) . '</b></td></tr>
        <tr><td>NIK</td><td>:</td><td>' . htmlspecialchars($nik) . '</td></tr>
        <tr><td>Tempat/Tanggal</td><td>:</td><td>' . htmlspecialchars($tempat) . ', ' . htmlspecialchars($tgl) . '</td></tr>
        <tr><td>Sebab</td><td>:</td><td>' . htmlspecialchars($sebab) . '</td></tr>
    </table>
    <br>
    <div>Demikian surat ini dibuat dengan sebenarnya.</div>
    ';

    $html .= suratFooterTTD($data['ttd'] ?? []);
    return $html;
}
