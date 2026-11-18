<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BeritaController extends Controller
{
    /**
     * POST /api/beritas/bulk
     * Terima teks multi-baris, pecah per baris, simpan sebagai banyak berita.
     */
    public function bulkStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'teks'     => 'required|string',
            'kategori' => 'nullable|string|max:100',
            'sumber'   => 'nullable|string|max:100',
        ]);

        // Pecah per baris, bersihkan spasi/baris kosong
        $baris = collect(explode("\n", $validated['teks']))
            ->map(fn ($line) => trim($line))
            ->filter(fn ($line) => $line !== '')
            ->values();

        if ($baris->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Teks tidak boleh kosong.',
            ], 422);
        }

        $created = [];
        foreach ($baris as $konten) {
            $created[] = Berita::create([
                'konten'   => $konten,
                'kategori' => $validated['kategori'] ?? null,
                'sumber'   => $validated['sumber'] ?? null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berita berhasil disimpan.',
            'total'   => count($created),
            'data'    => $created,   // array of objects
        ], 201);
    }

    /**
     * GET /api/beritas
     * Kembalikan semua berita dalam format array.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Berita::query();

        // Optional filter kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $beritas = $query->latest()->get();

        // Mode ringkas: hanya array string konten
        if ($request->boolean('simple')) {
            return response()->json([
                'success' => true,
                'total'   => $beritas->count(),
                'data'    => $beritas->pluck('konten')->toArray(), // array of string
            ]);
        }

        return response()->json([
            'success' => true,
            'total'   => $beritas->count(),
            'data'    => $beritas->toArray(), // array of objects
        ]);
    }

    /**
     * GET /api/beritas/{id}
     */
    public function show(int $id): JsonResponse
    {
        $berita = Berita::find($id);

        if (! $berita) {
            return response()->json([
                'success' => false,
                'message' => 'Berita tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $berita,
        ]);
    }
}
