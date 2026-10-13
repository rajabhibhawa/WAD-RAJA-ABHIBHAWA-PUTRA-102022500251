<?php
require 'koneksi.php';

$produk = $pdo->query("SELECT * FROM produk ORDER BY id DESC")->fetchAll();

$pesan = $_SESSION['pesan'] ?? null;
$tipe  = $_SESSION['pesan_tipe'] ?? 'sukses';
unset($_SESSION['pesan'], $_SESSION['pesan_tipe']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Daftar Produk</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="header">
  <h1>UMKM Toko Online - Modul Katalog Produk</h1>
</div>

<div class="container">
  <div class="toolbar">
    <h2>Daftar Produk</h2>
    <a href="produk.php" class="btn">+ Tambah Produk</a>
  </div>

  <?php if ($pesan): ?>
    <div class="alert <?= $tipe === 'hapus' ? 'alert-error' : 'alert-sukses' ?>"><?= e($pesan) ?></div>
  <?php endif; ?>

  <div class="table-wrap">
    <table>
      <tr>
        <th class="tengah">No</th>
        <th>Gambar Produk</th>
        <th>Nama Produk</th>
        <th>SKU</th>
        <th>Merek</th>
        <th>Varian</th>
        <th>Berat</th>
        <th>Harga</th>
        <th class="tengah">Stok</th>
        <th class="tengah">Aksi</th>
      </tr>

      <?php if (!$produk): ?>
        <tr><td colspan="10">Belum ada produk.</td></tr>
      <?php endif; ?>

      <?php foreach ($produk as $i => $p): ?>
      <tr>
        <td class="tengah"><?= $i + 1 ?></td>
        <td>
          <?php if ($p['foto']): ?>
            <img src="uploads/<?= e($p['foto']) ?>" alt="<?= e($p['nama']) ?>">
          <?php else: ?>
            -
          <?php endif; ?>
        </td>
        <td><?= e($p['nama']) ?></td>
        <td><?= e($p['sku']) ?></td>
        <td><?= e($p['merek']) ?></td>
        <td><?= e($p['warna']) ?> / <?= e($p['ukuran']) ?></td>
        <td><?= e($p['berat']) ?> gram</td>
        <td class="harga">Rp <?= number_format($p['harga'], 0, ',', '.') ?></td>
        <td class="tengah"><?= e($p['stok']) ?></td>
        <td class="tengah">
          <div class="aksi">
            <a href="produk.php?id=<?= (int)$p['id'] ?>" class="btn-edit">Edit</a>
            <form action="hapus.php" method="post" class="inline"
                  onsubmit="return confirm('Hapus produk ini?')">
              <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
              <button type="submit" class="btn-hapus">Hapus</button>
            </form>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>

</body>
</html>