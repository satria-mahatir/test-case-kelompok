<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Penerbit;
use App\Models\Penulis;
use App\Models\Peminjaman;
use Illuminate\Database\Seeder;

class PerpustakaanSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Kategori (5)
        $kategoris = ['Fiksi', 'Non-Fiksi', 'Sains', 'Sejarah', 'Teknologi'];
        $katIds = [];
        foreach ($kategoris as $k) {
            $katIds[] = Kategori::create(['nama_kategori' => $k])->id;
        }

        // 2. Penulis (5)
        $penulis = [
            ['nama_penulis' => 'Pramoedya Ananta Toer', 'email' => 'pramoedya@example.com'],
            ['nama_penulis' => 'Tere Liye', 'email' => 'tereliye@example.com'],
            ['nama_penulis' => 'Andrea Hirata', 'email' => 'andrea@example.com'],
            ['nama_penulis' => 'J.K. Rowling', 'email' => 'jkrowling@example.com'],
            ['nama_penulis' => 'B.J. Habibie', 'email' => 'habibie@example.com'],
        ];
        $penIds = [];
        foreach ($penulis as $p) {
            $penIds[] = Penulis::create($p)->id;
        }

        // 3. Penerbit (10 Penerbit dengan Kota)
        $penerbits = [
            ['nama_penerbit' => 'Gramedia Pustaka Utama', 'kota' => 'Jakarta'],
            ['nama_penerbit' => 'Penerbit Erlangga', 'kota' => 'Jakarta'],
            ['nama_penerbit' => 'Mizan Publishing', 'kota' => 'Bandung'],
            ['nama_penerbit' => 'Bentang Pustaka', 'kota' => 'Yogyakarta'],
            ['nama_penerbit' => 'Republika Penerbit', 'kota' => 'Jakarta'],
            ['nama_penerbit' => 'Kompas Ilmu', 'kota' => 'Surabaya'],
            ['nama_penerbit' => 'Deepublish', 'kota' => 'Yogyakarta'],
            ['nama_penerbit' => 'Andi Publisher', 'kota' => 'Semarang'],
            ['nama_penerbit' => 'Penerbit Inari', 'kota' => 'Depok'],
            ['nama_penerbit' => 'GagasMedia', 'kota' => 'Jakarta Selatan'],
        ];
        $penrIds = [];
        foreach ($penerbits as $pn) {
            $penrIds[] = Penerbit::create($pn)->id;
        }

        // 4. Buku (15 Buku)
        for ($i = 1; $i <= 15; $i++) {
            Buku::create([
                'judul' => "Buku Contoh Ke-$i",
                'kategori_id' => $katIds[array_rand($katIds)],
                'penulis_id' => $penIds[array_rand($penIds)],
                'penerbit_id' => $penrIds[array_rand($penrIds)],
                'isbn' => '978-0' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'tahun_terbit' => rand(2000, 2024),
                'stok' => rand(2, 10),
                'deskripsi' => 'Ini adalah deskripsi dummy untuk buku contoh ke-' . $i,
            ]);
        }

        // 5. Peminjaman (8 Sampel Transaksi)
        $sampleLoans = [
            [
                'buku_id' => 1,
                'nama_peminjam' => 'Budi Santoso',
                'nis' => '2026001',
                'tanggal_pinjam' => now()->subDays(3)->toDateString(),
                'tanggal_kembali_rencana' => now()->addDays(4)->toDateString(),
                'tanggal_pengembalian' => null,
                'status' => 'dipinjam',
            ],
            [
                'buku_id' => 2,
                'nama_peminjam' => 'Siti Rahma',
                'nis' => '2026002',
                'tanggal_pinjam' => now()->subDays(10)->toDateString(),
                'tanggal_kembali_rencana' => now()->subDays(3)->toDateString(),
                'tanggal_pengembalian' => now()->subDays(2)->toDateString(),
                'status' => 'dikembalikan',
            ],
            [
                'buku_id' => 3,
                'nama_peminjam' => 'Doni Kusuma',
                'nis' => '2026003',
                'tanggal_pinjam' => now()->subDays(12)->toDateString(),
                'tanggal_kembali_rencana' => now()->subDays(5)->toDateString(),
                'tanggal_pengembalian' => null,
                'status' => 'dipinjam', // akan terdeteksi sebagai terlambat
            ],
            [
                'buku_id' => 4,
                'nama_peminjam' => 'Rina Wijaya',
                'nis' => '2026004',
                'tanggal_pinjam' => now()->subDays(1)->toDateString(),
                'tanggal_kembali_rencana' => now()->addDays(6)->toDateString(),
                'tanggal_pengembalian' => null,
                'status' => 'dipinjam',
            ],
            [
                'buku_id' => 5,
                'nama_peminjam' => 'Andi Pratama',
                'nis' => '2026005',
                'tanggal_pinjam' => now()->subDays(15)->toDateString(),
                'tanggal_kembali_rencana' => now()->subDays(8)->toDateString(),
                'tanggal_pengembalian' => now()->subDays(7)->toDateString(),
                'status' => 'dikembalikan',
            ],
            [
                'buku_id' => 6,
                'nama_peminjam' => 'Eko Prasetyo',
                'nis' => '2026006',
                'tanggal_pinjam' => now()->subDays(14)->toDateString(),
                'tanggal_kembali_rencana' => now()->subDays(7)->toDateString(),
                'tanggal_pengembalian' => null,
                'status' => 'dipinjam', // akan terdeteksi sebagai terlambat
            ],
            [
                'buku_id' => 7,
                'nama_peminjam' => 'Maya Putri',
                'nis' => '2026007',
                'tanggal_pinjam' => now()->subDays(2)->toDateString(),
                'tanggal_kembali_rencana' => now()->addDays(5)->toDateString(),
                'tanggal_pengembalian' => null,
                'status' => 'dipinjam',
            ],
            [
                'buku_id' => 8,
                'nama_peminjam' => 'Fajar Hidayat',
                'nis' => '2026008',
                'tanggal_pinjam' => now()->subDays(20)->toDateString(),
                'tanggal_kembali_rencana' => now()->subDays(13)->toDateString(),
                'tanggal_pengembalian' => now()->subDays(12)->toDateString(),
                'status' => 'dikembalikan',
            ],
        ];

        foreach ($sampleLoans as $loan) {
            Peminjaman::create($loan);
        }
    }
}