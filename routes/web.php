<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\ItemDocumentoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard/dados', [DashboardController::class, 'dados'])->name('dashboard.dados');
Route::get('/dashboard/itens', [DashboardController::class, 'itensDoMes'])->name('dashboard.itens');

Route::resource('documentos', DocumentoController::class);
Route::get('documentos/{documento}/arquivo', [DocumentoController::class, 'arquivo'])
    ->name('documentos.arquivo');
Route::post('documentos/{documento}/reprocessar', [DocumentoController::class, 'reprocessar'])
    ->name('documentos.reprocessar');

Route::post('documentos/{documento}/itens', [ItemDocumentoController::class, 'store'])
    ->name('itens.store');
Route::put('itens/{item}', [ItemDocumentoController::class, 'update'])
    ->name('itens.update');
Route::delete('itens/{item}', [ItemDocumentoController::class, 'destroy'])
    ->name('itens.destroy');
