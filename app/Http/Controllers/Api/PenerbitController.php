<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PenerbitRequest;
use App\Models\Penerbit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PenerbitController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Penerbit::query();

        if ($request->filled('search')) {
            $query->where('nama_penerbit', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->integer('per_page', 10);
        $penerbits = $query->orderBy('nama_penerbit')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar penerbit',
            'data' => $penerbits->items(),
            'pagination' => [
                'current_page' => $penerbits->currentPage(),
                'per_page' => $penerbits->perPage(),
                'total' => $penerbits->total(),
                'last_page' => $penerbits->lastPage(),
            ],
        ]);
    }

    public function store(PenerbitRequest $request): JsonResponse
    {
        $penerbit = Penerbit::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Penerbit berhasil ditambahkan',
            'data' => $penerbit,
        ], 201);
    }

    public function show(Penerbit $penerbit): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $penerbit,
        ]);
    }

    public function update(PenerbitRequest $request, Penerbit $penerbit): JsonResponse
    {
        $penerbit->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Penerbit berhasil diperbarui',
            'data' => $penerbit,
        ]);
    }

    public function destroy(Penerbit $penerbit): JsonResponse
    {
        $penerbit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Penerbit berhasil dihapus',
        ]);
    }
}
