<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bukus', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 200);
            $table->foreignId('kategori_id')->constrained('kategoris')->onDelete('cascade');
            $table->foreignId('penulis_id')->constrained('penulis')->onDelete('cascade');
            $table->foreignId('penerbit_id')->constrained('penerbits')->onDelete('cascade');
            $table->string('isbn', 20)->unique();
            $table->year('tahun_terbit');
            $table->integer('stok')->default(0);
            $table->text('deskripsi')->nullable();
            $table->string('cover')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bukus');
    }
};
