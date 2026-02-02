
<?php
require 'config.php';

$stmt = $pdo->prepare("
    INSERT INTO transaksi (tanggal, jenis, kategori, nominal, keterangan)
    VALUES (?, ?, ?, ?, ?)
");

$stmt->execute([
    $_POST['tanggal'],
    $_POST['jenis'],
    $_POST['kategori'],
    $_POST['nominal'],
    $_POST['keterangan']
]);

header("Location: index.php");
