
<?php
require 'config.php';

$bulan = $_GET['bulan'] ?? '';
$tahun = $_GET['tahun'] ?? '';

$where = "";
$params = [];

if ($bulan && $tahun) {
    $where = "WHERE MONTH(tanggal) = ? AND YEAR(tanggal) = ?";
    $params = [$bulan, $tahun];
}
$saldo = $pdo->query("
    SELECT 
    SUM(CASE WHEN jenis='pemasukan' THEN nominal ELSE 0 END) -
    SUM(CASE WHEN jenis='pengeluaran' THEN nominal ELSE 0 END)
    AS saldo
    FROM transaksi
")->fetch()['saldo'];


$stmt = $pdo->prepare("SELECT * FROM transaksi $where ORDER BY tanggal DESC");
$stmt->execute($params);
$data = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Catatan Keuangan</title>
</head>
<body>

<h2>Saldo Saat Ini: Rp <?= number_format($saldo ?? 0) ?></h2>

<h3>Tambah Transaksi</h3>
<form action="simpan.php" method="post">
    <input type="date" name="tanggal" required><br><br>

    <select name="jenis" required>
        <option value="pemasukan">Pemasukan</option>
        <option value="pengeluaran">Pengeluaran</option>
    </select><br><br>

    <input type="text" name="kategori" placeholder="Kategori"><br><br>

    <input type="number" name="nominal" placeholder="Nominal" required><br><br>

    <textarea name="keterangan" placeholder="Keterangan"></textarea><br><br>

    <button type="submit">Simpan</button>
</form>

<hr>

<h3>Filter Bulan</h3>

<form method="get">
    <select name="bulan">
        <option value="">-- Pilih Bulan --</option>
        <?php
        for ($i = 1; $i <= 12; $i++) {
            $selected = ($bulan == $i) ? 'selected' : '';
            echo "<option value='$i' $selected>$i</option>";
        }
        ?>
    </select>

    <select name="tahun">
        <option value="">-- Pilih Tahun --</option>
        <?php
        $tahun_sekarang = date('Y');
        for ($y = $tahun_sekarang; $y >= $tahun_sekarang - 5; $y--) {
            $selected = ($tahun == $y) ? 'selected' : '';
            echo "<option value='$y' $selected>$y</option>";
        }
        ?>
    </select>

    <button type="submit">Filter</button>
    <a href="index.php">Reset</a>
</form>

<form method="get">
    <select name="bulan">
        <option value="">-- Pilih Bulan --</option>
        <?php
        for ($i = 1; $i <= 12; $i++) {
            $selected = ($bulan == $i) ? 'selected' : '';
            echo "<option value='$i' $selected>$i</option>";
        }
        ?>
    </select>

    <select name="tahun">
        <option value="">-- Pilih Tahun --</option>
        <?php
        $tahun_sekarang = date('Y');
        for ($y = $tahun_sekarang; $y >= $tahun_sekarang - 5; $y--) {
            $selected = ($tahun == $y) ? 'selected' : '';
            echo "<option value='$y' $selected>$y</option>";
        }
        ?>
    </select>

    <button type="submit">Filter</button>

    <a href="export.php?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>">
        Export CSV
    </a>

    <a href="index.php">Reset</a>
</form>
<hr>
<h3>Daftar Transaksi</h3>
<table border="1" cellpadding="5">
<tr>
    <th>Tanggal</th>
    <th>Jenis</th>
    <th>Kategori</th>
    <th>Nominal</th>
    <th>Keterangan</th>
    <th>Aksi</th>
</tr>

<?php foreach ($data as $row): ?>
<tr>
    <td><?= $row['tanggal'] ?></td>
    <td><?= $row['jenis'] ?></td>
    <td><?= $row['kategori'] ?></td>
    <td>Rp <?= number_format($row['nominal']) ?></td>
    <td><?= $row['keterangan'] ?></td>
    <td>
<a href="edit.php?id=<?= $row['id'] ?>">Edit</a> |
        <a href="hapus.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus data?')">
            Hapus
        </a>
    </td>
</tr>
<?php endforeach; ?>

</table>

</body>
</html>
