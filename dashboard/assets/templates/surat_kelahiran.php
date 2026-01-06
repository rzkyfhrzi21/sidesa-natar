<?php
require_once __DIR__ . '/_layout.php';

function templateSuratKelahiran($data)
{
    $no = $data['nomor_surat'] ?? '-';
    $namaBayi = $data['nama_bayi'] ?? '-';
    $tgl = $data['tanggal_kelahiran'] ?? '-';
    $tempat = $data['tempat_kelahiran'] ?? '-';
    $ayah = $data['nama_ayah'] ?? '-';
    $ibu  = $data['nama_ibu'] ?? '-';

    $html = suratHeader($data['desa'] ?? []);
    $html .= '
    <div style="text-align:center;">
        <h3 style="margin:0;">SURAT KETERANGAN KELAHIRAN</h3>
        <div>Nomor: <b>' . htmlspecialchars($no) . '</b></div>
    </div>
    <br>
    <table cellpadding="3">
        <tr><td width="35%">Nama Bayi</td><td width="5%">:</td><td width="60%"><b>' . htmlspecialchars($namaBayi) . '</b></td></tr>
        <tr><td>Tempat, Tanggal Lahir</td><td>:</td><td>' . htmlspecialchars($tempat) . ', ' . htmlspecialchars($tgl) . '</td></tr>
        <tr><td>Nama Ayah</td><td>:</td><td>' . htmlspecialchars($ayah) . '</td></tr>
        <tr><td>Nama Ibu</td><td>:</td><td>' . htmlspecialchars($ibu) . '</td></tr>
    </table>
    <br>
    <div>Demikian surat keterangan ini dibuat untuk dipergunakan sebagaimana mestinya.</div>
    ';

    $html .= suratFooterTTD($data['ttd'] ?? []);
    return $html;
}
