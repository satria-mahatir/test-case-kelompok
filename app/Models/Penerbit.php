<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penerbit extends Model
{
    use HasFactory;

    protected $table = 'penerbits';

    protected $fillable = ['nama_penerbit', 'kota'];

    public function bukus(): HasMany
    {
        return $this->hasMany(Buku::class);
    }
}
