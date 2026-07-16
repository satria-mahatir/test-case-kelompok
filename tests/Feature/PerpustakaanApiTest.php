<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Penerbit;
use App\Models\Penulis;
use App\Models\Peminjaman;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerpustakaanApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function test_kategori_crud_operations()
    {
        // 1. Store Kategori
        $response = $this->postJson('/api/v1/kategoris', [
            'nama_kategori' => 'Sains & Teknologi'
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Kategori berhasil ditambahkan'
                 ]);

        $kategoriId = $response->json('data.id');
        $this->assertDatabaseHas('kategoris', [
            'id' => $kategoriId,
            'nama_kategori' => 'Sains & Teknologi'
        ]);

        // 2. Index Kategori (Search)
        $response = $this->getJson('/api/v1/kategoris?search=Sains');
        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data');

        // 3. Show Kategori
        $response = $this->getJson('/api/v1/kategoris/' . $kategoriId);
        $response->assertStatus(200)
                 ->assertJsonPath('data.nama_kategori', 'Sains & Teknologi');

        // 4. Update Kategori (with Unique Validation and same name)
        $response = $this->putJson('/api/v1/kategoris/' . $kategoriId, [
            'nama_kategori' => 'Sains & Teknologi' // should be allowed because it is the same Kategori
        ]);
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Kategori berhasil diperbarui'
                 ]);

        // Update Kategori with different name
        $response = $this->putJson('/api/v1/kategoris/' . $kategoriId, [
            'nama_kategori' => 'Sains Komputer'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('kategoris', [
            'id' => $kategoriId,
            'nama_kategori' => 'Sains Komputer'
        ]);

        // 5. Delete Kategori
        $response = $this->deleteJson('/api/v1/kategoris/' . $kategoriId);
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Kategori berhasil dihapus'
                 ]);

        $this->assertDatabaseMissing('kategoris', ['id' => $kategoriId]);
    }

    /** @test */
    public function test_penulis_crud_operations()
    {
        // 1. Store Penulis
        $response = $this->postJson('/api/v1/penulis', [
            'nama_penulis' => 'Tere Liye',
            'email' => 'tereliye@example.com'
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Penulis berhasil ditambahkan'
                 ]);

        $penulisId = $response->json('data.id');

        // 2. Update Penulis
        $response = $this->putJson('/api/v1/penulis/' . $penulisId, [
            'nama_penulis' => 'Tere Liye Updated',
            'email' => 'tereliye.updated@example.com'
        ]);
        $response->assertStatus(200);

        // 3. Show Penulis
        $response = $this->getJson('/api/v1/penulis/' . $penulisId);
        $response->assertStatus(200)
                 ->assertJsonPath('data.nama_penulis', 'Tere Liye Updated');

        // 4. Delete Penulis
        $response = $this->deleteJson('/api/v1/penulis/' . $penulisId);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('penulis', ['id' => $penulisId]);
    }

    /** @test */
    public function test_penerbit_crud_operations()
    {
        // 1. Store Penerbit
        $response = $this->postJson('/api/v1/penerbits', [
            'nama_penerbit' => 'Gramedia Pustaka',
            'kota' => 'Jakarta'
        ]);

        $response->assertStatus(201);
        $penerbitId = $response->json('data.id');

        // 2. Update Penerbit
        $response = $this->putJson('/api/v1/penerbits/' . $penerbitId, [
            'nama_penerbit' => 'Gramedia Pustaka Utama',
            'kota' => 'Bandung'
        ]);
        $response->assertStatus(200);

        // 3. Show Penerbit
        $response = $this->getJson('/api/v1/penerbits/' . $penerbitId);
        $response->assertStatus(200)
                 ->assertJsonPath('data.nama_penerbit', 'Gramedia Pustaka Utama');

        // 4. Delete Penerbit
        $response = $this->deleteJson('/api/v1/penerbits/' . $penerbitId);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('penerbits', ['id' => $penerbitId]);
    }

    /** @test */
    public function test_buku_crud_operations()
    {
        $kategori = Kategori::create(['nama_kategori' => 'Fiksi']);
        $penulis = Penulis::create(['nama_penulis' => 'Andrea Hirata']);
        $penerbit = Penerbit::create(['nama_penerbit' => 'Bentang Pustaka']);

        // 1. Store Buku
        $response = $this->postJson('/api/v1/bukus', [
            'judul' => 'Laskar Pelangi',
            'kategori_id' => $kategori->id,
            'penulis_id' => $penulis->id,
            'penerbit_id' => $penerbit->id,
            'isbn' => '978-602-291-282-8',
            'tahun_terbit' => 2005,
            'stok' => 5,
            'deskripsi' => 'Kisah perjuangan anak-anak Belitong.',
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Buku berhasil ditambahkan'
                 ]);

        $bukuId = $response->json('data.id');

        // 2. Show Buku
        $response = $this->getJson('/api/v1/bukus/' . $bukuId);
        $response->assertStatus(200)
                 ->assertJsonPath('data.judul', 'Laskar Pelangi')
                 ->assertJsonPath('data.isbn', '978-602-291-282-8');

        // 3. Update Buku (using same ISBN -> unique validation should NOT fail)
        $response = $this->putJson('/api/v1/bukus/' . $bukuId, [
            'judul' => 'Laskar Pelangi Edisi Baru',
            'kategori_id' => $kategori->id,
            'penulis_id' => $penulis->id,
            'penerbit_id' => $penerbit->id,
            'isbn' => '978-602-291-282-8',
            'tahun_terbit' => 2005,
            'stok' => 10,
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Buku berhasil diperbarui'
                 ]);

        $this->assertDatabaseHas('bukus', [
            'id' => $bukuId,
            'judul' => 'Laskar Pelangi Edisi Baru',
            'stok' => 10
        ]);

        // 4. Delete Buku
        $response = $this->deleteJson('/api/v1/bukus/' . $bukuId);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('bukus', ['id' => $bukuId]);
    }

    /** @test */
    public function test_peminjaman_transaksi_operations()
    {
        $kategori = Kategori::create(['nama_kategori' => 'Teknologi']);
        $penulis = Penulis::create(['nama_penulis' => 'Budi']);
        $penerbit = Penerbit::create(['nama_penerbit' => 'Ristek']);

        $buku = Buku::create([
            'judul' => 'Belajar Laravel 10',
            'kategori_id' => $kategori->id,
            'penulis_id' => $penulis->id,
            'penerbit_id' => $penerbit->id,
            'isbn' => '978-111-222-333-4',
            'tahun_terbit' => 2023,
            'stok' => 1,
            'deskripsi' => 'Panduan belajar Laravel.',
        ]);

        // 1. Lakukan peminjaman sukses (stok berkurang)
        $response = $this->postJson('/api/v1/peminjaman', [
            'buku_id' => $buku->id,
            'nama_peminjam' => 'Andi',
            'nis' => 'NIS12345',
            'tanggal_pinjam' => '2026-07-16',
            'tanggal_kembali_rencana' => '2026-07-23',
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Peminjaman berhasil dicatat'
                 ]);

        $peminjamanId = $response->json('data.id');

        // Pastikan stok berkurang menjadi 0
        $this->assertEquals(0, $buku->fresh()->stok);

        // 2. Lakukan peminjaman lagi untuk buku yang sama (stok habis, harus gagal 422)
        $response = $this->postJson('/api/v1/peminjaman', [
            'buku_id' => $buku->id,
            'nama_peminjam' => 'Budi',
            'nis' => 'NIS12346',
            'tanggal_pinjam' => '2026-07-16',
            'tanggal_kembali_rencana' => '2026-07-23',
        ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Stok buku tidak tersedia'
                 ]);

        // 3. Kembalikan buku (stok bertambah kembali)
        $response = $this->patchJson("/api/v1/peminjaman/{$peminjamanId}/kembalikan");
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Buku berhasil dikembalikan'
                 ]);

        // Pastikan stok bertambah kembali menjadi 1
        $this->assertEquals(1, $buku->fresh()->stok);
        $this->assertEquals('dikembalikan', Peminjaman::find($peminjamanId)->status);

        // 4. Coba kembalikan lagi (harus gagal 422)
        $response = $this->patchJson("/api/v1/peminjaman/{$peminjamanId}/kembalikan");
        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Buku ini sudah dikembalikan sebelumnya'
                 ]);
    }
}
