<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);

    $stmt = $pdo->prepare("SELECT foto FROM produk WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if ($row) {
        $pdo->prepare("DELETE FROM produk WHERE id = ?")->execute([$id]);

        if ($row['foto'] && file_exists(__DIR__ . '/uploads/' . $row['foto'])) {
            unlink(__DIR__ . '/uploads/' . $row['foto']);
        }
        $_SESSION['pesan'] = 'Produk berhasil dihapus.';
        $_SESSION['pesan_tipe'] = 'hapus';
    }
}

header('Location: index.php');
exit;