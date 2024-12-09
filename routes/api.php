<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocsController;
use Spatie\Permission\Models\Role;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->post('/upload-docs', [DocsController::class, 'uploadDocs'])->name('upload-docs');
Route::middleware(['auth:sanctum'])->post('/upload-docs-extra', [DocsController::class, 'uploadDocsExtra'])->name('upload-docs-extra');
Route::middleware(['auth:sanctum'])->post('/set-file', [DocsController::class, 'setFile'])->name('set-file');
Route::middleware(['auth:sanctum'])->get('/get-file/{folio}', [DocsController::class, 'getFile'])->name('get-file');

