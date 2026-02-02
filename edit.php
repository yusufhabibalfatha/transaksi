
<?php
require 'config.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM transaksi WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) {
    die("Data tidak ditemukan");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Transaksi</title>
</head>
<body>

<h3>Edit Transaksi</h3>

<form action="update.php" method="post">
    <input type="hidden" name="id" value="<?= $data['id'] ?>">

    <input type="date" name="tanggal" value="<?= $data['tanggal'] ?>" required><br><br>

    <select name="jenis" required>
        <option value="pemasukan" <?= $data['jenis']=='pemasukan'?'selected':'' ?>>
            Pemasukan
        </option>
        <option value="pengeluaran" <?= $data['jenis']=='pengeluaran'?'selected':'' ?>>
            Pengeluaran
        </option>
    </select><br><br>

    <input type="text" name="kategori" value="<?= $data['kategori'] ?>"><br><br>

    <input type="number" name="nominal" value="<?= $data['nominal'] ?>" required><br><br>

    <textarea name="keterangan"><?= $data['keterangan'] ?></textarea><br><br>

    <button type="submit">Update</button>
    <a href="index.php">Batal</a>
</form>

</body>
</html>
