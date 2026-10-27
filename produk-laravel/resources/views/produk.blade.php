<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Form Produk</title>
<link rel="stylesheet" href="{{ asset('style.css') }}">
</head>
<body>

<div class="header">
  <h1>UMKM Toko Online - Modul Katalog Produk</h1>
</div>

<div class="container">
  <h2>{{ $id ? 'Edit Produk' : 'Tambah Produk' }}</h2>

  @if ($errors->any())
    <div class="alert alert-error">
      @foreach ($errors->all() as $err)
        <div>{{ $err }}</div>
      @endforeach
    </div>
  @endif

  <form action="{{ $id ? route('produk.form', ['id' => $id]) : route('produk.form') }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="form-grid">
      <div class="field full">
        <label for="nama">Nama Produk</label>
        <input id="nama" name="nama" type="text" value="{{ old('nama', $data->nama) }}">
      </div>

      <div class="field">
        <label for="sku">SKU</label>
        <input id="sku" name="sku" type="text" value="{{ old('sku', $data->sku) }}" placeholder="KTS-001" required>
      </div>

      <div class="field">
        <label for="merek">Merek</label>
        <input id="merek" name="merek" type="text" value="{{ old('merek', $data->merek) }}" required>
      </div>

      <div class="field">
        <label for="kategori">Kategori</label>
        <select id="kategori" name="kategori">
          @foreach ($daftarKategori as $k)
            <option {{ old('kategori', $data->kategori) === $k ? 'selected' : '' }}>{{ $k }}</option>
          @endforeach
        </select>
      </div>

      <div class="field">
        <label for="berat">Berat (gram)</label>
        <input id="berat" name="berat" type="number" min="1" value="{{ old('berat', $data->berat) }}" placeholder="200" required>
      </div>

      <div class="field">
        <label for="warna">Warna</label>
        <input id="warna" name="warna" type="text" value="{{ old('warna', $data->warna) }}" placeholder="Hitam" required>
      </div>

      <div class="field">
        <label for="ukuran">Ukuran</label>
        <select id="ukuran" name="ukuran">
          @foreach ($daftarUkuran as $u)
            <option {{ old('ukuran', $data->ukuran) === $u ? 'selected' : '' }}>{{ $u }}</option>
          @endforeach
        </select>
      </div>

      <div class="field">
        <label for="stok">Stok Varian</label>
        <input id="stok" name="stok" type="number" min="0" value="{{ old('stok', $data->stok) }}" placeholder="40" required>
      </div>

      <div class="field">
        <label for="harga">Harga Jual (Rp)</label>
        <input id="harga" name="harga" type="number" min="1" value="{{ old('harga', $data->harga) }}" placeholder="75000" required>
      </div>

      <div class="field full">
        <label for="foto">Foto Produk</label>
        @if ($data->foto)
          <img src="{{ asset('uploads/' . $data->foto) }}" alt="Foto saat ini" class="foto-preview">
        @endif
        <input id="foto" name="foto" type="file" accept="image/jpeg,image/png,image/webp">
      </div>
    </div>

    <div class="form-actions">
      <a href="{{ route('produk.index') }}" class="btn-secondary">Batal</a>
      <button type="submit" class="btn">Simpan</button>
    </div>
  </form>
</div>

</body>
</html>
