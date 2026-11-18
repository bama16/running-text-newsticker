<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkStoreBeritaRequest;
use App\Http\Requests\StoreBeritaRequest;
use App\Http\Requests\UpdateBeritaRequest;
use App\Http\Resources\BeritaResource;
use App\Models\Berita;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class BeritaController extends Controller
{
    /**
     * GET /api/beritas
     * List semua berita (array). Mendukung filter & mode simple.
     */
    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $query = Berita::query();

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('sumber')) {
            $query->where('sumber', $request->sumber);
        }

        if ($request->filled('q')) {
            $query->where('konten', 'like', '%' . $request->q . '%');
        }

        $beritas = $query->latest()->get();

        // Mode simple: array of string
        if ($request->boolean('simple')) {
            return response()->json([
                'success' => true,
                'total'   => $beritas->count(),
                'data'    => $beritas->pluck('konten')->toArray(),
            ]);
        }

        return BeritaResource::collection($beritas);
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
            'data'    => new BeritaResource($berita),
        ]);
    }

    /**
     * POST /api/beritas
     * Create satu berita (single input).
     */
    public function store(StoreBeritaRequest $request): JsonResponse
    {
        $berita = Berita::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Berita berhasil dibuat.',
            'data'    => new BeritaResource($berita),
        ], 201);
    }

    /**
     * POST /api/beritas/bulk
     * Create banyak berita sekaligus (teks multi-baris).
     */
    public function bulkStore(BulkStoreBeritaRequest $request): JsonResponse
    {
        $validated = $request->validated();

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

        $created = DB::transaction(function () use ($baris, $validated) {
            $rows = [];
            foreach ($baris as $konten) {
                $rows[] = Berita::create([
                    'konten'   => $konten,
                    'kategori' => $validated['kategori'] ?? null,
                    'sumber'   => $validated['sumber'] ?? null,
                ]);
            }
            return $rows;
        });

        return response()->json([
            'success' => true,
            'message' => 'Berita berhasil disimpan.',
            'total'   => count($created),
            'data'    => BeritaResource::collection(collect($created)),
        ], 201);
    }

    /**
     * PUT/PATCH /api/beritas/{id}
     */
    public function update(UpdateBeritaRequest $request, int $id): JsonResponse
    {
        $berita = Berita::find($id);

        if (! $berita) {
            return response()->json([
                'success' => false,
                'message' => 'Berita tidak ditemukan.',
            ], 404);
        }

        $berita->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Berita berhasil diperbarui.',
            'data'    => new BeritaResource($berita->fresh()),
        ]);
    }

    /**
     * DELETE /api/beritas/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $berita = Berita::find($id);

        if (! $berita) {
            return response()->json([
                'success' => false,
                'message' => 'Berita tidak ditemukan.',
            ], 404);
        }

        $berita->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berita berhasil dihapus.',
        ]);
    }
}
