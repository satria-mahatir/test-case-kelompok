<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PeminjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'buku_id' => 'required|integer|exists:bukus,id',
            'user_id' => 'nullable|integer|exists:users,id',
            'nama_peminjam' => 'nullable|string|max:150',
            'nis' => 'nullable|integer',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali_rencana' => 'required|date|after_or_equal:tanggal_pinjam',
        ];
    }

    public function messages(): array
    {
        return [
            'nis.integer' => 'NIS harus berupa angka (integer)',
            'buku_id.required' => 'Buku wajib dipilih',
            'buku_id.exists' => 'Buku yang dipilih tidak ditemukan',
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
