<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\KategoriRequest;
use App\Models\Kategori;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    // GET /api/kategoris?search=&per_page=
    public function index(Request $request): JsonResponse
    {
        $query = Kategori::query();

        if ($request->filled('search')) {
            $query->where('nama_kategori', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->integer('per_page', 10);
        $kategoris = $query->orderBy('nama_kategori')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar kategori',
            'data' => $kategoris->items(),
            'pagination' => [
                'current_page' => $kategoris->currentPage(),
                'per_page' => $kategoris->perPage(),
                'total' => $kategoris->total(),
                'last_page' => $kategoris->lastPage(),
            ],
        ]);
    }

    public function store(KategoriRequest $request): JsonResponse
    {
        $kategori = Kategori::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan',
            'data' => $kategori,
        ], 201);
    }

    public function show(Kategori $kategori): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $kategori,
        ]);
    }

    public function update(KategoriRequest $request, Kategori $kategori): JsonResponse
    {
        $kategori->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil diperbarui',
            'data' => $kategori,
        ]);
    }

    public function destroy(Kategori $kategori): JsonResponse
    {
        $kategori->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus',
        ]);
    }
}
