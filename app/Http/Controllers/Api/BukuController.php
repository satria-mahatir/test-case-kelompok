<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BukuRequest;
use App\Http\Resources\BukuResource;
use App\Models\Buku;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    // GET /api/bukus?search=&kategori_id=&per_page=&page=
    public function index(Request $request): JsonResponse
    {
        $query = Buku::with(['kategori', 'penulis', 'penerbit']);

        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('judul', 'like', "%{$keyword}%")
                  ->orWhere('isbn', 'like', "%{$keyword}%")
                  ->orWhereHas('penulis', function ($sub) use ($keyword) {
                      $sub->where('nama_penulis', 'like', "%{$keyword}%");
                  });
            });
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        $perPage = $request->integer('per_page', 10);
        $bukus = $query->orderBy('judul')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar buku',
            'data' => BukuResource::collection($bukus->items()),
            'pagination' => [
                'current_page' => $bukus->currentPage(),
                'per_page' => $bukus->perPage(),
                'total' => $bukus->total(),
                'last_page' => $bukus->lastPage(),
            ],
        ]);
    }

    public function store(BukuRequest $request): JsonResponse
    {
        $buku = Buku::create($request->validated());
        $buku->load(['kategori', 'penulis', 'penerbit']);

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil ditambahkan',
            'data' => new BukuResource($buku),
        ], 201);
    }

    public function show(Buku $buku): JsonResponse
    {
        $buku->load(['kategori', 'penulis', 'penerbit']);

        return response()->json([
            'success' => true,
            'data' => new BukuResource($buku),
        ]);
    }

    public function update(BukuRequest $request, Buku $buku): JsonResponse
    {
        $buku->update($request->validated());
        $buku->load(['kategori', 'penulis', 'penerbit']);

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil diperbarui',
            'data' => new BukuResource($buku),
        ]);
    }

    public function destroy(Buku $buku): JsonResponse
    {
        $buku->delete();

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil dihapus',
        ]);
    }
}
