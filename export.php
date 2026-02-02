
<?php
require 'config.php';

$bulan = $_GET['bulan'] ?? '';
$tahun = $_GET['tahun'] ?? '';

$where = "";
$params = [];

$nama_file = "keuangan-semua-data.csv";

if ($bulan && $tahun) {
    $where = "WHERE MONTH(tanggal) = ? AND YEAR(tanggal) = ?";
    $params = [$bulan, $tahun];
    $nama_file = "keuangan-$tahun-" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . ".csv";
}

$stmt = $pdo->prepare("
    SELECT tanggal, jenis, kategori, nominal, keterangan
    FROM transaksi
    $where
    ORDER BY tanggal ASC
");
$stmt->execute($params);

// Header CSV
header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=$nama_file");

// UTF-8 BOM (penting untuk Excel)
echo "\xEF\xBB\xBF";

$output = fopen("php://output", "w");

// Header kolom
fputcsv($output, ['Tanggal', 'Jenis', 'Kategori', 'Nominal', 'Keterangan']);

// Data
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, $row);
}

fclose($output);
exit;
