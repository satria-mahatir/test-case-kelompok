<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BukuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $buku = $this->route('buku');
        $id = $buku instanceof \App\Models\Buku ? $buku->id : $buku;
        // saat update, method PUT/PATCH -> field boleh 'sometimes'
        $isUpdate = in_array($this->method(), ['PUT', 'PATCH']);

        return [
            'judul' => ($isUpdate ? 'sometimes|' : '') . 'required|string|max:200',
            'kategori_id' => ($isUpdate ? 'sometimes|' : '') . 'required|integer|exists:kategoris,id',
            'penulis_id' => ($isUpdate ? 'sometimes|' : '') . 'required|integer|exists:penulis,id',
            'penerbit_id' => ($isUpdate ? 'sometimes|' : '') . 'required|integer|exists:penerbits,id',
            'isbn' => 'nullable|string|max:20|unique:bukus,isbn,' . $id,
            'tahun_terbit' => 'nullable|digits:4|integer|min:1900|max:' . (date('Y') + 1),
            'stok' => ($isUpdate ? 'sometimes|' : '') . 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'cover' => 'nullable|string|max:255',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $validator->errors(),
        ], 422));
    }
}
