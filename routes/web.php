<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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
});
