<?php
require 'koneksi.php';

$daftarMerek    = ['Brand A', 'Brand B', 'Brand C'];
$daftarKategori = ['Fashion', 'Elektronik', 'Makanan', 'Kerajinan'];
$daftarUkuran   = ['S', 'M', 'L', 'XL'];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$data = [
    'nama' => '', 'sku' => '', 'merek' => 'Brand A', 'kategori' => 'Fashion',
    'berat' => '', 'warna' => '', 'ukuran' => 'S', 'stok' => '',
    'harga' => '', 'foto' => null,
];

// Mode edit: ambil data lama
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM produk WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) {
        $_SESSION['pesan'] = 'Produk tidak ditemukan.';
        header('Location: index.php');
        exit;
    }
    $data = $row;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fotoLama = $data['foto'];

    $data['nama']     = trim($_POST['nama'] ?? '');
    $data['sku']      = trim($_POST['sku'] ?? '');
    $data['merek']    = $_POST['merek'] ?? '';
    $data['kategori'] = $_POST['kategori'] ?? '';
    $data['berat']    = $_POST['berat'] ?? '';
    $data['warna']    = trim($_POST['warna'] ?? '');
    $data['ukuran']   = $_POST['ukuran'] ?? '';
    $data['stok']     = $_POST['stok'] ?? '';
    $data['harga']    = $_POST['harga'] ?? '';

    // Validasi
    if ($data['nama'] === '')  $errors[] = 'Nama produk wajib diisi.';
    if ($data['sku'] === '')   $errors[] = 'SKU wajib diisi.';
    if (!in_array($data['merek'], $daftarMerek, true))       $errors[] = 'Merek tidak valid.';
    if (!in_array($data['kategori'], $daftarKategori, true)) $errors[] = 'Kategori tidak valid.';
    if (!in_array($data['ukuran'], $daftarUkuran, true))     $errors[] = 'Ukuran tidak valid.';
    if (!ctype_digit((string)$data['berat']) || $data['berat'] < 1) $errors[] = 'Berat harus angka lebih dari 0.';
    if (!ctype_digit((string)$data['stok']))                 $errors[] = 'Stok harus angka 0 atau lebih.';
    if (!ctype_digit((string)$data['harga']) || $data['harga'] < 1) $errors[] = 'Harga harus angka lebih dari 0.';
    if ($data['warna'] === '') $errors[] = 'Warna wajib diisi.';

    // Validasi foto (opsional)
    $namaFileBaru = null;
    if (!empty($_FILES['foto']['name'])) {
        if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Upload foto gagal.';
        } else {
            $ext  = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $mime = mime_content_type($_FILES['foto']['tmp_name']);
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)
                || !in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
                $errors[] = 'Foto harus berformat JPG, PNG, atau WEBP.';
            } elseif ($_FILES['foto']['size'] > 2 * 1024 * 1024) {
                $errors[] = 'Ukuran foto maksimal 2 MB.';
            } else {
                $namaFileBaru = uniqid('p_', true) . '.' . $ext;
            }
        }
    }

    if (!$errors) {
        try {
            if ($namaFileBaru) {
                move_uploaded_file($_FILES['foto']['tmp_name'], __DIR__ . '/uploads/' . $namaFileBaru);
                $data['foto'] = $namaFileBaru;
            }

            if ($id) {
                $sql = "UPDATE produk SET nama=?, sku=?, merek=?, kategori=?, berat=?, warna=?,
                        ukuran=?, stok=?, harga=?, foto=? WHERE id=?";
                $params = [$data['nama'], $data['sku'], $data['merek'], $data['kategori'],
                           $data['berat'], $data['warna'], $data['ukuran'], $data['stok'],
                           $data['harga'], $data['foto'], $id];
                $pesan = 'Produk berhasil diperbarui.';
            } else {
                $sql = "INSERT INTO produk (nama, sku, merek, kategori, berat, warna, ukuran, stok, harga, foto)
                        VALUES (?,?,?,?,?,?,?,?,?,?)";
                $params = [$data['nama'], $data['sku'], $data['merek'], $data['kategori'],
                           $data['berat'], $data['warna'], $data['ukuran'], $data['stok'],
                           $data['harga'], $data['foto']];
                $pesan = 'Produk berhasil ditambahkan.';
            }

            $pdo->prepare($sql)->execute($params);

            // Hapus foto lama jika diganti
            if ($namaFileBaru && $fotoLama && file_exists(__DIR__ . '/uploads/' . $fotoLama)) {
                unlink(__DIR__ . '/uploads/' . $fotoLama);
            }

            $_SESSION['pesan'] = $pesan;
            header('Location: index.php');
            exit;
        } catch (PDOException $ex) {
            if ($ex->getCode() === '23000') {
                $errors[] = 'SKU sudah dipakai produk lain.';
            } else {
                $errors[] = 'Gagal menyimpan data: ' . $ex->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Form Produk</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="header">
  <h1>UMKM Toko Online - Modul Katalog Produk</h1>
</div>

<div class="container">
  <h2><?= $id ? 'Edit Produk' : 'Tambah Produk' ?></h2>

  <?php if ($errors): ?>
    <div class="alert alert-error">
      <?php foreach ($errors as $err): ?>
        <div><?= e($err) ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <div class="form-grid">
      <div class="field full">
        <label for="nama">Nama Produk</label>
        <input id="nama" name="nama" type="text" value="<?= e($data['nama']) ?>"
               placeholder="Contoh: Kaos Polos Katun Combed" required>
      </div>

      <div class="field">
        <label for="sku">SKU</label>
        <input id="sku" name="sku" type="text" value="<?= e($data['sku']) ?>" placeholder="KTS-001" required>
      </div>

      <div class="field">
        <label for="merek">Merek</label>
        <select id="merek" name="merek">
          <?php foreach ($daftarMerek as $m): ?>
            <option <?= $data['merek'] === $m ? 'selected' : '' ?>><?= e($m) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="field">
        <label for="kategori">Kategori</label>
        <select id="kategori" name="kategori">
          <?php foreach ($daftarKategori as $k): ?>
            <option <?= $data['kategori'] === $k ? 'selected' : '' ?>><?= e($k) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="field">
        <label for="berat">Berat (gram)</label>
        <input id="berat" name="berat" type="number" min="1" value="<?= e($data['berat']) ?>" placeholder="200" required>
      </div>

      <div class="field">
        <label for="warna">Warna</label>
        <input id="warna" name="warna" type="text" value="<?= e($data['warna']) ?>" placeholder="Hitam" required>
      </div>

      <div class="field">
        <label for="ukuran">Ukuran</label>
        <select id="ukuran" name="ukuran">
          <?php foreach ($daftarUkuran as $u): ?>
            <option <?= $data['ukuran'] === $u ? 'selected' : '' ?>><?= e($u) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="field">
        <label for="stok">Stok Varian</label>
        <input id="stok" name="stok" type="number" min="0" value="<?= e($data['stok']) ?>" placeholder="40" required>
      </div>

      <div class="field">
        <label for="harga">Harga Jual (Rp)</label>
        <input id="harga" name="harga" type="number" min="1" value="<?= e($data['harga']) ?>" placeholder="75000" required>
      </div>

      <div class="field full">
        <label for="foto">Foto Produk</label>
        <?php if ($data['foto']): ?>
          <img src="uploads/<?= e($data['foto']) ?>" alt="Foto saat ini" class="foto-preview">
        <?php endif; ?>
        <input id="foto" name="foto" type="file" accept="image/jpeg,image/png,image/webp">
      </div>
    </div>

    <div class="form-actions">
      <a href="index.php" class="btn-secondary">Batal</a>
      <button type="submit" class="btn">Simpan</button>
    </div>
  </form>
</div>

</body>
</html>