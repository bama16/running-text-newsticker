<?php

use App\Http\Controllers\Api\BeritaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::prefix('beritas')->group(function () {
//     Route::get('/', [BeritaController::class, 'index']);
//     Route::get('/{id}', [BeritaController::class, 'show'])->whereNumber('id');
//     Route::post('/bulk', [BeritaController::class, 'bulkStore']);
// });

Route::prefix('beritas')->group(function () {
    // READ
    Route::get('/',        [BeritaController::class, 'index']);
    Route::get('/{id}',    [BeritaController::class, 'show'])->whereNumber('id');

    // CREATE
    Route::post('/',       [BeritaController::class, 'store']);
    Route::post('/bulk',   [BeritaController::class, 'bulkStore']); // bulk multi-baris

    // UPDATE
    Route::match(['put', 'patch'], '/{id}', [BeritaController::class, 'update'])
        ->whereNumber('id');

    // DELETE
    Route::delete('/{id}', [BeritaController::class, 'destroy'])
        ->whereNumber('id');
});