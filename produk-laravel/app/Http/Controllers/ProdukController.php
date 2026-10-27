<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProdukController extends Controller
{
    /**
     * @var list<string>
     */
    private array $daftarKategori = ['Fashion', 'Elektronik', 'Makanan', 'Kerajinan', 'Lainnya'];

    /**
     * @var list<string>
     */
    private array $daftarUkuran = ['S', 'M', 'L', 'XL', '-'];

    public function index(): View
    {
        $produk = Produk::orderByDesc('id')->get();

        return view('index', [
            'produk' => $produk,
        ]);
    }

    public function form(Request $request, ?int $id = null): View|RedirectResponse
    {
        $id = $id ?: (int) $request->query('id', 0);

        $data = new Produk([
            'nama' => '',
            'sku' => '',
            'merek' => '',
            'kategori' => 'Fashion',
            'berat' => '',
            'warna' => '',
            'ukuran' => 'S',
            'stok' => '',
            'harga' => '',
            'foto' => '',
        ]);

        if ($id > 0) {
            $produk = Produk::find($id);

            if (! $produk) {
                return redirect()->route('produk.index')
                    ->with('pesan', 'Produk tidak ditemukan.');
            }

            $data = $produk;
        }

        return view('produk', [
            'id' => $id,
            'data' => $data,
            'daftarKategori' => $this->daftarKategori,
            'daftarUkuran' => $this->daftarUkuran,
        ]);
    }

    public function simpan(Request $request, ?int $id = null): RedirectResponse
    {
        $id = $id ?: (int) $request->query('id', $request->input('id', 0));
        $produk = null;

        if ($id > 0) {
            $produk = Produk::find($id);

            if (! $produk) {
                return redirect()->route('produk.index')
                    ->with('pesan', 'Produk tidak ditemukan.');
            }
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'sku' => [
                'required',
                'string',
                'max:50',
                Rule::unique('produk', 'sku')->ignore($id),
            ],
            'merek' => ['required', 'string', 'max:50'],
            'kategori' => ['required', Rule::in($this->daftarKategori)],
            'ukuran' => ['required', Rule::in($this->daftarUkuran)],
            'berat' => ['required', 'integer', 'min:1'],
            'stok' => ['required', 'integer', 'min:0'],
            'harga' => ['required', 'integer', 'min:1'],
            'warna' => ['required', 'string', 'max:50'],
            'foto' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'nama.required' => 'Nama produk wajib diisi.',
            'sku.required' => 'SKU wajib diisi.',
            'sku.unique' => 'SKU sudah dipakai produk lain.',
            'merek.required' => 'Merek tidak valid.',
            'kategori.required' => 'Kategori tidak valid.',
            'kategori.in' => 'Kategori tidak valid.',
            'ukuran.required' => 'Ukuran tidak valid.',
            'ukuran.in' => 'Ukuran tidak valid.',
            'berat.required' => 'Berat harus angka lebih dari 0.',
            'berat.integer' => 'Berat harus angka lebih dari 0.',
            'berat.min' => 'Berat harus angka lebih dari 0.',
            'stok.required' => 'Stok harus angka 0 atau lebih.',
            'stok.integer' => 'Stok harus angka 0 atau lebih.',
            'stok.min' => 'Stok harus angka 0 atau lebih.',
            'harga.required' => 'Harga harus angka lebih dari 0.',
            'harga.integer' => 'Harga harus angka lebih dari 0.',
            'harga.min' => 'Harga harus angka lebih dari 0.',
            'warna.required' => 'Warna wajib diisi.',
            'foto.uploaded' => 'Upload foto gagal.',
            'foto.mimes' => 'Foto harus berformat JPG, PNG, atau WEBP.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        $fotoLama = $produk?->foto;
        $namaFileBaru = null;

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $ext = strtolower($file->getClientOriginalExtension());
            $namaFileBaru = uniqid('p_', true).'.'.$ext;

            $uploadDir = public_path('uploads');
            if (! is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $file->move($uploadDir, $namaFileBaru);
            $validated['foto'] = $namaFileBaru;
        } else {
            $validated['foto'] = $fotoLama;
        }

        if ($produk) {
            $produk->update($validated);
            $pesan = 'Produk berhasil diperbarui.';
        } else {
            Produk::create($validated);
            $pesan = 'Produk berhasil ditambahkan.';
        }

        if ($namaFileBaru && $fotoLama && file_exists(public_path('uploads/'.$fotoLama))) {
            unlink(public_path('uploads/'.$fotoLama));
        }

        return redirect()->route('produk.index')
            ->with('pesan', $pesan);
    }

    public function hapus(Request $request, ?int $id = null): RedirectResponse
    {
        $id = $id ?: (int) $request->input('id', 0);
        $produk = Produk::find($id);

        if ($produk) {
            $foto = $produk->foto;
            $produk->delete();

            if ($foto && file_exists(public_path('uploads/'.$foto))) {
                unlink(public_path('uploads/'.$foto));
            }

            return redirect()->route('produk.index')
                ->with('pesan', 'Produk berhasil dihapus.')
                ->with('pesan_tipe', 'hapus');
        }

        return redirect()->route('produk.index');
    }
}
