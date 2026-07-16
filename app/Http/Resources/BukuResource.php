<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BukuResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'judul' => $this->judul,
            'isbn' => $this->isbn,
            'tahun_terbit' => $this->tahun_terbit,
            'stok' => $this->stok,
            'deskripsi' => $this->deskripsi,
            'cover' => $this->cover,
            'kategori' => [
                'id' => $this->kategori?->id,
                'nama_kategori' => $this->kategori?->nama_kategori,
            ],
            'penulis' => [
                'id' => $this->penulis?->id,
                'nama_penulis' => $this->penulis?->nama_penulis,
            ],
            'penerbit' => [
                'id' => $this->penerbit?->id,
                'nama_penerbit' => $this->penerbit?->nama_penerbit,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
