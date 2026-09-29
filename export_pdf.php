<?php
require 'config.php';
require 'includes/functions.php';
require 'vendor/fpdf/fpdf.php';

$bulan = isset($_GET['bulan']) && $_GET['bulan'] !== '' ? (int)$_GET['bulan'] : (int)date('n');
$tahun = isset($_GET['tahun']) && $_GET['tahun'] !== '' ? (int)$_GET['tahun'] : (int)date('Y');
$statusFilter = $_GET['status'] ?? '';
$petugasFilter = isset($_GET['petugas_id']) ? (int)$_GET['petugas_id'] : 0;

$where = "MONTH(j.tanggal) = ? AND YEAR(j.tanggal) = ?";
$types = "ii";
$params = [$bulan, $tahun];

if ($statusFilter !== '' && in_array($statusFilter, ['Terjadwal','Selesai','Dibatalkan'])) {
    $where .= " AND j.status = ?";
    $types .= "s";
    $params[] = $statusFilter;
}
if ($petugasFilter > 0) {
    $where .= " AND j.petugas_id = ?";
    $types .= "i";
    $params[] = $petugasFilter;
}

$sql = "SELECT j.no_spt, j.tanggal, j.tanggal_pulang, p.nama AS nama_petugas, p.jabatan, j.kegiatan, j.lokasi, j.status
        FROM jadwal j JOIN petugas p ON p.id = j.petugas_id
        WHERE $where
        ORDER BY j.tanggal ASC, j.tanggal_pulang ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();
$daftar = [];
while ($row = $res->fetch_assoc()) $daftar[] = $row;
$stmt->close();

// Nama petugas untuk sub-judul (jika difilter per petugas)
$namaPetugasFilter = '';
if ($petugasFilter > 0) {
    $stmtP = $conn->prepare("SELECT nama FROM petugas WHERE id = ?");
    $stmtP->bind_param("i", $petugasFilter);
    $stmtP->execute();
    $rowP = $stmtP->get_result()->fetch_assoc();
    $stmtP->close();
    if ($rowP) $namaPetugasFilter = $rowP['nama'];
}

// FPDF (font standar) memakai encoding Windows-1252/Latin-1, bukan UTF-8,
// jadi semua teks yang dicetak harus dikonversi dulu lewat fungsi ini.
function t($teks) {
    $hasil = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT', (string)$teks);
    return $hasil === false ? (string)$teks : $hasil;
}

// Memotong teks agar muat dalam satu baris sel (menghindari kebutuhan hitung tinggi baris dinamis)
function potong($pdf, $teks, $lebarMaks) {
    $teks = t($teks);
    if ($pdf->GetStringWidth($teks) <= $lebarMaks) return $teks;
    while ($teks !== '' && $pdf->GetStringWidth($teks . '...') > $lebarMaks) {
        $teks = substr($teks, 0, -1);
    }
    return $teks . '...';
}

class RekapPDF extends FPDF {
    public $judulBulan = '';
    public $subJudul = '';
    public $kolom = [];   // ['label' => .., 'lebar' => ..]

    function Header() {
        $this->SetFont('Helvetica', 'B', 13);
        $this->Cell(0, 6, t('DISNAKERTRANS PROVINSI KALIMANTAN SELATAN'), 0, 1, 'C');
        $this->SetFont('Helvetica', '', 10);
        $this->Cell(0, 5, t('Rekap Jadwal Tugas Lapangan'), 0, 1, 'C');
        $this->SetFont('Helvetica', 'B', 10);
        $this->Cell(0, 5, t($this->judulBulan), 0, 1, 'C');
        if ($this->subJudul !== '') {
            $this->SetFont('Helvetica', '', 9);
            $this->Cell(0, 5, t($this->subJudul), 0, 1, 'C');
        }
        $this->Ln(2);

        $this->SetFont('Helvetica', 'B', 8);
        $this->SetFillColor(230, 230, 230);
        foreach ($this->kolom as $k) {
            $this->Cell($k['lebar'], 7, t($k['label']), 1, 0, 'C', true);
        }
        $this->Ln();
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Helvetica', 'I', 8);
        $this->Cell(0, 5, t('Dicetak: ' . date('d-m-Y H:i')), 0, 0, 'L');
        $this->Cell(0, 5, t('Halaman ' . $this->PageNo() . '/{nb}'), 0, 0, 'R');
    }
}

$judulBulan = namaBulan($bulan) . ' ' . $tahun;
$subJudulParts = [];
if ($statusFilter !== '') $subJudulParts[] = 'Status: ' . $statusFilter;
if ($namaPetugasFilter !== '') $subJudulParts[] = 'Petugas: ' . $namaPetugasFilter;
$subJudul = implode('   |   ', $subJudulParts);

$kolom = [
    ['label' => 'No',          'lebar' => 8,  'align' => 'C'],
    ['label' => 'No. SPT',     'lebar' => 26, 'align' => 'L'],
    ['label' => 'Tanggal',     'lebar' => 24, 'align' => 'C'],
    ['label' => 'Tgl Pulang',  'lebar' => 24, 'align' => 'C'],
    ['label' => 'Petugas',     'lebar' => 45, 'align' => 'L'],
    ['label' => 'Jabatan',     'lebar' => 55, 'align' => 'L'],
    ['label' => 'Kegiatan',    'lebar' => 45, 'align' => 'L'],
    ['label' => 'Lokasi',      'lebar' => 35, 'align' => 'L'],
    ['label' => 'Status',      'lebar' => 15, 'align' => 'C'],
];

$pdf = new RekapPDF('L', 'mm', 'A4'); // Landscape agar kolom lebih lega
$pdf->AliasNbPages();
$pdf->judulBulan = $judulBulan;
$pdf->subJudul = $subJudul;
$pdf->kolom = $kolom;
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 18);
$pdf->AddPage();

$tinggiBaris = 7;
$pdf->SetFont('Helvetica', '', 8);

if (empty($daftar)) {
    $lebarTotal = array_sum(array_column($kolom, 'lebar'));
    $pdf->Cell($lebarTotal, 8, t('Tidak ada data untuk filter yang dipilih.'), 1, 1, 'C');
} else {
    $no = 1;
    foreach ($daftar as $d) {
        $nilai = [
            (string)$no,
            (string)($d['no_spt'] ?? ''),
            formatTanggalPendek($d['tanggal']),
            formatTanggalPendek($d['tanggal_pulang']),
            $d['nama_petugas'],
            $d['jabatan'],
            $d['kegiatan'],
            $d['lokasi'],
            $d['status'],
        ];
        foreach ($kolom as $i => $k) {
            $teks = potong($pdf, $nilai[$i], $k['lebar'] - 2);
            $pdf->Cell($k['lebar'], $tinggiBaris, $teks, 1, 0, $k['align']);
        }
        $pdf->Ln();
        $no++;
    }
}

$namaFile = 'rekap_jadwal_tl_' . namaBulan($bulan) . '_' . $tahun . '.pdf';
$pdf->Output('D', $namaFile);
exit;
