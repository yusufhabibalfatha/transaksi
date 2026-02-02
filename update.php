
<?php
require 'config.php';

$stmt = $pdo->prepare("
    UPDATE transaksi 
    SET tanggal = ?, jenis = ?, kategori = ?, nominal = ?, keterangan = ?
    WHERE id = ?
");

$stmt->execute([
    $_POST['tanggal'],
    $_POST['jenis'],
    $_POST['kategori'],
    $_POST['nominal'],
    $_POST['keterangan'],
    $_POST['id']
]);

header("Location: index.php");
