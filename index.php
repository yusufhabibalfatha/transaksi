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
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>

    <div class="saldo-display">
        <h2>Saldo Saat Ini</h2>
        <div class="saldo-amount">Rp <?= number_format($saldo ?? 0) ?></div>
    </div>

    <h3>Tambah Transaksi</h3>
    <form action="simpan.php" method="post">
        <label for="tanggal">Tanggal:</label>
        <input type="date" name="tanggal" id="tanggal" required>

        <label for="jenis">Jenis:</label>
        <select name="jenis" id="jenis" required>
            <option value="pemasukan">Pemasukan</option>
            <option value="pengeluaran">Pengeluaran</option>
        </select>

        <label for="kategori">Kategori:</label>
        <input type="text" name="kategori" id="kategori" placeholder="Kategori">

        <label for="nominal">Nominal:</label>
        <input type="number" name="nominal" id="nominal" placeholder="Nominal" required>

        <label for="keterangan">Keterangan:</label>
        <textarea name="keterangan" id="keterangan" placeholder="Keterangan"></textarea>

        <button type="submit" class="btn-primary">Simpan</button>
    </form>

    <div class="filter-form">
        <h3>Filter Bulan</h3>
        <form method="get">
            <div class="button-group">
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

                <button type="submit" class="btn-secondary">Filter</button>

                <a href="export.php?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="btn-success">
                    Export CSV
                </a>

                <a href="index.php" class="btn-secondary">Reset</a>
            </div>
        </form>
    </div>

    <h3>Daftar Transaksi</h3>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Kategori</th>
                    <th>Nominal</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row) : ?>
                    <tr>
                        <td><?= $row['tanggal'] ?></td>
                        <td class="jenis-<?= $row['jenis'] ?>"><?= $row['jenis'] ?></td>
                        <td><?= $row['kategori'] ?></td>
                        <td class="nominal">Rp <?= number_format($row['nominal']) ?></td>
                        <td><?= $row['keterangan'] ?></td>
                        <td>
                            <div class="aksi-buttons">
                                <a href="edit.php?id=<?= $row['id'] ?>" class="edit-btn">Edit</a>
                                <a href="hapus.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus data?')" class="delete-btn">Hapus</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>

</html>