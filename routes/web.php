<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/berita'));

Route::get('/berita', fn () => view('berita.index'))->name('berita.index');