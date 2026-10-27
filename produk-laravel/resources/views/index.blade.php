<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Daftar Produk</title>
<link rel="stylesheet" href="{{ asset('style.css') }}">
</head>
<body>

<div class="header">
  <h1>UMKM Toko Online - Modul Katalog Produk</h1>
</div>

<div class="container">
  <div class="toolbar">
    <h2>Daftar Produk</h2>
    <a href="{{ route('produk.form') }}" class="btn">+ Tambah Produk</a>
  </div>

  @if (session('pesan'))
    <div class="alert {{ session('pesan_tipe') === 'hapus' ? 'alert-error' : 'alert-sukses' }}">{{ session('pesan') }}</div>
  @endif

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

      @if ($produk->isEmpty())
        <tr><td colspan="10">Belum ada produk.</td></tr>
      @endif

      @foreach ($produk as $i => $p)
      <tr>
        <td class="tengah">{{ $i + 1 }}</td>
        <td>
          @if ($p->foto)
            <img src="{{ asset('uploads/' . $p->foto) }}" alt="{{ $p->nama }}">
          @else
            -
          @endif
        </td>
        <td>{{ $p->nama }}</td>
        <td>{{ $p->sku }}</td>
        <td>{{ $p->merek }}</td>
        <td>{{ $p->warna }} / {{ $p->ukuran }}</td>
        <td>{{ $p->berat }} gram</td>
        <td class="harga">Rp {{ number_format($p->harga, 0, ',', '.') }}</td>
        <td class="tengah">{{ $p->stok }}</td>
        <td class="tengah">
          <div class="aksi">
            <a href="{{ route('produk.form', ['id' => (int) $p->id]) }}" class="btn-edit">Edit</a>
            <form action="{{ route('produk.hapus') }}" method="post" class="inline"
                  onsubmit="return confirm('Hapus produk ini?')">
              @csrf
              <input type="hidden" name="id" value="{{ (int) $p->id }}">
              <button type="submit" class="btn-hapus">Hapus</button>
            </form>
          </div>
        </td>
      </tr>
      @endforeach
    </table>
  </div>
</div>

</body>
</html>
