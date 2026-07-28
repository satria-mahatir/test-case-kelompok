<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PeminjamanResource extends JsonResource
{
    public function toArray($request)
    {
        $status = $this->status;
        if ($status === 'dipinjam' && $this->tanggal_kembali_rencana) {
            if ($this->tanggal_kembali_rencana->lt(now()->startOfDay())) {
                $status = 'terlambat';
            }
        }

        $namaPeminjam = $this->nama_peminjam ?? $this->user?->name ?? 'Peminjam';
        $nis = $this->nis ?? $this->user?->username ?? '-';

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'nama_peminjam' => $namaPeminjam,
            'nis' => $nis,
            'tanggal_pinjam' => $this->tanggal_pinjam?->format('Y-m-d'),
            'tanggal_kembali_rencana' => $this->tanggal_kembali_rencana?->format('Y-m-d'),
            'tanggal_pengembalian' => $this->tanggal_pengembalian?->format('Y-m-d'),
            'status' => $status,
            'buku' => [
                'id' => $this->buku?->id,
                'judul' => $this->buku?->judul,
                'kategori' => $this->buku?->kategori ? [
                    'id' => $this->buku->kategori->id,
                    'nama_kategori' => $this->buku->kategori->nama_kategori,
                ] : null,
                'penulis' => $this->buku?->penulis ? [
                    'id' => $this->buku->penulis->id,
                    'nama_penulis' => $this->buku->penulis->nama_penulis,
                ] : null,
                'penerbit' => $this->buku?->penerbit ? [
                    'id' => $this->buku->penerbit->id,
                    'nama_penerbit' => $this->buku->penerbit->nama_penerbit,
                ] : null,
            ],
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'username' => $this->user->username,
                'role' => $this->user->role,
            ] : null,
        ];
    }
}
