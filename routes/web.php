<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocsController;

Route::get('/', function () {
    return view('auth.login');
});

// Route::post('/upload-docs', [DocsController::class, 'uploadDocsWeb'])->name('upload-docs');
Route::middleware(['auth'])->post('/upload-docs', [DocsController::class, 'uploadDocsWeb'])->name('upload.docs');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('files');
    })->name('dashboard');
});
