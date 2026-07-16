<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjamen';

    protected $fillable = [
        'buku_id',
        'nama_peminjam',
        'nis',
        'tanggal_pinjam',
        'tanggal_kembali_rencana',
        'tanggal_pengembalian',
        'status',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali_rencana' => 'date',
        'tanggal_pengembalian' => 'date',
    ];

    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class);
    }
}
