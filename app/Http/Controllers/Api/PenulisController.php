<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PenulisRequest;
use App\Models\Penulis;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PenulisController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Penulis::query();

        if ($request->filled('search')) {
            $query->where('nama_penulis', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->integer('per_page', 10);
        $penuliss = $query->orderBy('nama_penulis')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar penulis',
            'data' => $penuliss->items(),
            'pagination' => [
                'current_page' => $penuliss->currentPage(),
                'per_page' => $penuliss->perPage(),
                'total' => $penuliss->total(),
                'last_page' => $penuliss->lastPage(),
            ],
        ]);
    }

    public function store(PenulisRequest $request): JsonResponse
    {
        $penuliss = Penulis::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Penulis berhasil ditambahkan',
            'data' => $penuliss,
        ], 201);
    }

    public function show(Penulis $penulis): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $penulis,
        ]);
    }

    public function update(PenulisRequest $request, Penulis $penulis): JsonResponse
    {
        $penulis->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Penulis berhasil diperbarui',
            'data' => $penulis,
        ]);
    }

    public function destroy(Penulis $penulis): JsonResponse
    {
        $penulis->delete();

        return response()->json([
            'success' => true,
            'message' => 'Penulis berhasil dihapus',
        ]);
    }
}
