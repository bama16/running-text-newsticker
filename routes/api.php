<?php

use App\Http\Controllers\Api\BeritaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('beritas')->group(function () {
    Route::get('/', [BeritaController::class, 'index']);
    Route::get('/{id}', [BeritaController::class, 'show'])->whereNumber('id');
    Route::post('/bulk', [BeritaController::class, 'bulkStore']);
});