<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Penerbit;
use App\Models\Penulis;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PerpustakaanSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Akun Pengguna / User (Tanpa Gmail - Username & Password)
        $users = [
            [
                'name' => 'Satria Mahatir (Administrator)',
                'username' => 'admin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ],
            [
                'name' => 'Budi Santoso (Petugas Web)',
                'username' => 'petugas1',
                'password' => Hash::make('password123'),
                'role' => 'petugas',
            ],
            [
                'name' => 'Ahmad Rizky (Peminjam Mobile)',
                'username' => 'peminjam1',
                'password' => Hash::make('password123'),
                'role' => 'peminjam',
            ],
            [
                'name' => 'Dewi Lestari (Peminjam Mobile)',
                'username' => 'peminjam2',
                'password' => Hash::make('password123'),
                'role' => 'peminjam',
            ],
        ];

        $createdUsers = [];
        foreach ($users as $u) {
            $createdUsers[$u['username']] = User::create($u);
        }

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
                'tahun_terbit' => rand(2015, 2024),
                'isbn' => sprintf('978-%03d', $i),
                'stok' => rand(1, 10),
            ]);
        }

        // 5. Transaksi Peminjaman Sampel (Terhubung dengan akun peminjam)
        $peminjamanSamples = [
            [
                'buku_id' => 1,
                'user_id' => $createdUsers['peminjam1']->id,
                'nama_peminjam' => 'Ahmad Rizky',
                'nis' => 'peminjam1',
                'tanggal_pinjam' => now()->subDays(5)->format('Y-m-d'),
                'tanggal_kembali_rencana' => now()->addDays(2)->format('Y-m-d'),
                'status' => 'dipinjam',
            ],
            [
                'buku_id' => 2,
                'user_id' => $createdUsers['peminjam2']->id,
                'nama_peminjam' => 'Dewi Lestari',
                'nis' => 'peminjam2',
                'tanggal_pinjam' => now()->subDays(10)->format('Y-m-d'),
                'tanggal_kembali_rencana' => now()->subDays(3)->format('Y-m-d'),
                'tanggal_pengembalian' => now()->subDays(2)->format('Y-m-d'),
                'status' => 'dikembalikan',
            ],
            [
                'buku_id' => 3,
                'user_id' => $createdUsers['peminjam1']->id,
                'nama_peminjam' => 'Ahmad Rizky',
                'nis' => 'peminjam1',
                'tanggal_pinjam' => now()->subDays(12)->format('Y-m-d'),
                'tanggal_kembali_rencana' => now()->subDays(5)->format('Y-m-d'),
                'status' => 'terlambat',
            ],
            [
                'buku_id' => 4,
                'user_id' => $createdUsers['peminjam2']->id,
                'nama_peminjam' => 'Dewi Lestari',
                'nis' => 'peminjam2',
                'tanggal_pinjam' => now()->subDays(2)->format('Y-m-d'),
                'tanggal_kembali_rencana' => now()->addDays(5)->format('Y-m-d'),
                'status' => 'dipinjam',
            ],
            [
                'buku_id' => 5,
                'user_id' => $createdUsers['peminjam1']->id,
                'nama_peminjam' => 'Ahmad Rizky',
                'nis' => 'peminjam1',
                'tanggal_pinjam' => now()->subDays(15)->format('Y-m-d'),
                'tanggal_kembali_rencana' => now()->subDays(8)->format('Y-m-d'),
                'tanggal_pengembalian' => now()->subDays(7)->format('Y-m-d'),
                'status' => 'dikembalikan',
            ],
            [
                'buku_id' => 6,
                'user_id' => $createdUsers['peminjam2']->id,
                'nama_peminjam' => 'Dewi Lestari',
                'nis' => 'peminjam2',
                'tanggal_pinjam' => now()->subDays(14)->format('Y-m-d'),
                'tanggal_kembali_rencana' => now()->subDays(7)->format('Y-m-d'),
                'status' => 'terlambat',
            ],
        ];

        foreach ($peminjamanSamples as $pm) {
            Peminjaman::create($pm);
        }
    }
}