<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Penerbit;
use App\Models\Penulis;
use Illuminate\Database\Seeder;

class PerpustakaanSeeder extends Seeder
{
    public function run(): void
    {
        $kategoris = ['Fiksi', 'Non-Fiksi', 'Sains', 'Sejarah', 'Teknologi'];
        foreach ($kategoris as $k) {
            Kategori::create(['nama_kategori' => $k]);
        }

        $penulis = ['Pramoedya Ananta Toer', 'Tere Liye', 'Andrea Hirata', 'J.K. Rowling', 'Habibie'];
        foreach ($penulis as $p) {
            Penulis::create(['nama_penulis' => $p]);
        }

        $penerbits = ['Gramedia', 'Erlangga', 'Mizan', 'Bentang Pustaka'];
        foreach ($penerbits as $pn) {
            Penerbit::create(['nama_penerbit' => $pn]);
        }

        for ($i = 1; $i <= 15; $i++) {
            Buku::create([
                'judul' => "Buku Contoh Ke-$i",
                'kategori_id' => rand(1, count($kategoris)),
                'penulis_id' => rand(1, count($penulis)),
                'penerbit_id' => rand(1, count($penerbits)),
                'isbn' => '978-000-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'tahun_terbit' => rand(2000, 2024),
                'stok' => rand(0, 10),
                'deskripsi' => 'Ini adalah deskripsi dummy untuk buku contoh ke-' . $i,
            ]);
        }
    }
}