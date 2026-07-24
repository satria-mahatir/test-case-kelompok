<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PeminjamanRequest;
use App\Http\Resources\PeminjamanResource;
use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    // GET /api/peminjaman?search=&status=&per_page=
    public function index(Request $request): JsonResponse
    {
        $query = Peminjaman::with('buku');

        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_peminjam', 'like', "%{$keyword}%")
                  ->orWhere('nis', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('status')) {
            $statusFilter = $request->status;
            if ($statusFilter === 'terlambat') {
                $query->where(function ($q) {
                    $q->where('status', 'terlambat')
                      ->orWhere(function ($sub) {
                          $sub->where('status', 'dipinjam')
                              ->where('tanggal_kembali_rencana', '<', now()->toDateString());
                      });
                });
            } elseif ($statusFilter === 'dipinjam') {
                $query->where('status', 'dipinjam')
                      ->where('tanggal_kembali_rencana', '>=', now()->toDateString());
            } else {
                $query->where('status', $statusFilter);
            }
        }

        $perPage = $request->integer('per_page', 10);
        $peminjaman = $query->orderByDesc('tanggal_pinjam')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar peminjaman',
            'data' => PeminjamanResource::collection($peminjaman->items()),
            'pagination' => [
                'current_page' => $peminjaman->currentPage(),
                'per_page' => $peminjaman->perPage(),
                'total' => $peminjaman->total(),
                'last_page' => $peminjaman->lastPage(),
            ],
        ]);
    }

    // POST /api/peminjaman  -> transaksi peminjaman buku
    public function store(PeminjamanRequest $request): JsonResponse
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data) {
            $buku = Buku::lockForUpdate()->findOrFail($data['buku_id']);

            if ($buku->stok < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok buku tidak tersedia',
                ], 422);
            }

            $buku->decrement('stok');

            $status = 'dipinjam';
            if (isset($data['tanggal_kembali_rencana'])) {
                $tglRencana = \Carbon\Carbon::parse($data['tanggal_kembali_rencana'])->startOfDay();
                if ($tglRencana->lt(now()->startOfDay())) {
                    $status = 'terlambat';
                }
            }

            $peminjaman = Peminjaman::create([
                ...$data,
                'status' => $status,
            ]);
            $peminjaman->load('buku');

            return response()->json([
                'success' => true,
                'message' => 'Peminjaman berhasil dicatat',
                'data' => new PeminjamanResource($peminjaman),
            ], 201);
        });
    }

    public function show(Peminjaman $peminjaman): JsonResponse
    {
        $peminjaman->load('buku');

        return response()->json([
            'success' => true,
            'data' => new PeminjamanResource($peminjaman),
        ]);
    }

    // PATCH /api/peminjaman/{id}/kembalikan -> transaksi pengembalian buku
    public function kembalikan(Peminjaman $peminjaman): JsonResponse
    {
        if ($peminjaman->status === 'dikembalikan') {
            return response()->json([
                'success' => false,
                'message' => 'Buku ini sudah dikembalikan sebelumnya',
            ], 422);
        }

        return DB::transaction(function () use ($peminjaman) {
            $peminjaman->update([
                'status' => 'dikembalikan',
                'tanggal_pengembalian' => now()->toDateString(),
            ]);

            $peminjaman->buku()->increment('stok');
            $peminjaman->load('buku');

            return response()->json([
                'success' => true,
                'message' => 'Buku berhasil dikembalikan',
                'data' => new PeminjamanResource($peminjaman),
            ]);
        });
    }

    public function destroy(Peminjaman $peminjaman): JsonResponse
    {
        $peminjaman->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data peminjaman berhasil dihapus',
        ]);
    }
}
