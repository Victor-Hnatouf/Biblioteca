<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/catalogo', \App\Livewire\CatalogoComponent::class)->name('catalogo');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    Route::get('/editoras', \App\Livewire\EditorasComponent::class)->name('editoras');
    Route::get('/autores', \App\Livewire\AutoresComponent::class)->name('autores');
    Route::get('/livros', \App\Livewire\LivrosComponent::class)->name('livros');
    Route::get('/requisicoes', \App\Livewire\RequisicoesComponent::class)->name('requisicoes');

    Route::middleware(['admin'])->group(function () {
        Route::get('/utilizadores', \App\Livewire\UtilizadoresComponent::class)->name('utilizadores');
    });
});
