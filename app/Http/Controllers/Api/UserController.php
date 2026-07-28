<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Tampilkan daftar akun pengguna.
     */
    public function index(Request $request)
    {
        $query = User::query()->orderBy('id', 'asc');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 10);
        $users = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar akun pengguna berhasil dimuat',
            'data' => UserResource::collection($users->items()),
            'pagination' => [
                'total' => $users->total(),
                'count' => $users->count(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'total_pages' => $users->lastPage(),
            ],
        ]);
    }

    /**
     * Tambah akun pengguna baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:users,username',
            'password' => 'required|string|min:6',
            'role' => 'nullable|string|in:admin,petugas,peminjam',
        ], [
            'name.required' => 'Nama lengkap wajib diisi',
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan, gunakan username lain',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'role.in' => 'Role harus salah satu dari: admin, petugas, peminjam',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => strtolower(trim($validated['username'])),
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'] ?? 'peminjam',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Akun pengguna berhasil dibuat',
            'data' => new UserResource($user),
        ], 201);
    }

    /**
     * Detail akun pengguna.
     */
    public function show(User $user)
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail akun pengguna',
            'data' => new UserResource($user),
        ]);
    }

    /**
     * Update akun pengguna.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:100',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:6',
            'role' => 'nullable|string|in:admin,petugas,peminjam',
        ], [
            'name.required' => 'Nama lengkap wajib diisi',
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan oleh akun lain',
            'password.min' => 'Password minimal 6 karakter',
            'role.in' => 'Role harus salah satu dari: admin, petugas, peminjam',
        ]);

        $userData = [
            'name' => $validated['name'],
            'username' => strtolower(trim($validated['username'])),
            'role' => $validated['role'] ?? $user->role,
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        return response()->json([
            'success' => true,
            'message' => 'Akun pengguna berhasil diperbarui',
            'data' => new UserResource($user),
        ]);
    }

    /**
     * Hapus akun pengguna.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Akun pengguna berhasil dihapus',
        ]);
    }
}
