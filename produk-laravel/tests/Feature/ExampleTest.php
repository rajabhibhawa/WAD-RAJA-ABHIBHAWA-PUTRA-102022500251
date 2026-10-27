<?php

use App\Models\Produk;

test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('Daftar Produk');
});

test('can perform full crud on produk', function () {
    $this->get('/produk')->assertStatus(200)->assertSee('Tambah Produk');

    $this->post('/produk', [
        'nama' => 'Kaos Polos Katun Combed',
        'sku' => 'KTS-001',
        'merek' => 'Brand A',
        'kategori' => 'Fashion',
        'berat' => 200,
        'warna' => 'Hitam',
        'ukuran' => 'M',
        'stok' => 40,
        'harga' => 75000,
    ])->assertRedirect('/');

    $produk = Produk::where('sku', 'KTS-001')->first();
    expect($produk)->not->toBeNull();

    $this->get("/produk/{$produk->id}/edit")
        ->assertStatus(200)
        ->assertSee('Edit Produk')
        ->assertSee('Kaos Polos Katun Combed');

    $this->put("/produk/{$produk->id}", [
        'nama' => 'Kaos Polos Premium',
        'sku' => 'KTS-001',
        'merek' => 'Brand B',
        'kategori' => 'Fashion',
        'berat' => 250,
        'warna' => 'Putih',
        'ukuran' => 'L',
        'stok' => 25,
        'harga' => 85000,
    ])->assertRedirect('/');

    expect($produk->fresh()->nama)->toBe('Kaos Polos Premium');

    $this->delete("/produk/{$produk->id}")
        ->assertRedirect('/');

    expect(Produk::count())->toBe(0);
});
