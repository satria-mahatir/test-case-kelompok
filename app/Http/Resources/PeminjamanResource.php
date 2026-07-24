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

        return [
            'id' => $this->id,
            'nama_peminjam' => $this->nama_peminjam,
            'nis' => $this->nis,
            'tanggal_pinjam' => $this->tanggal_pinjam?->format('Y-m-d'),
            'tanggal_kembali_rencana' => $this->tanggal_kembali_rencana?->format('Y-m-d'),
            'tanggal_pengembalian' => $this->tanggal_pengembalian?->format('Y-m-d'),
            'status' => $status,
            'buku' => [
                'id' => $this->buku?->id,
                'judul' => $this->buku?->judul,
            ],
        ];
    }
}
