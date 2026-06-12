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
        if (auth()->user()?->isAdmin()) {
            return redirect()->route('gestao');
        }

        return redirect()->route('catalogo');
    })->name('dashboard');
    
    Route::get('/editoras', \App\Livewire\EditorasComponent::class)->name('editoras');
    Route::get('/autores', \App\Livewire\AutoresComponent::class)->name('autores');
    Route::get('/livros', \App\Livewire\LivrosComponent::class)->name('livros');
    Route::get('/requisicoes', \App\Livewire\RequisicoesComponent::class)->name('requisicoes');
    Route::get('/reviews', \App\Livewire\ReviewsComponent::class)->name('reviews');
    
    
    Route::get('/carrinho', \App\Livewire\CarrinhoComponent::class)->name('carrinho');
    Route::get('/carrinho/sucesso/{id}', [\App\Http\Controllers\CarrinhoSucessoController::class, 'sucesso'])->name('carrinho.sucesso');
    Route::get('/carrinho/cancelado/{id}', [\App\Http\Controllers\CarrinhoCanceladoController::class, 'cancelado'])->name('carrinho.cancelado');

    Route::middleware(['admin'])->group(function () {
        Route::get('/gestao', \App\Livewire\GestaoResumoComponent::class)->name('gestao');
        Route::get('/utilizadores', \App\Livewire\UtilizadoresComponent::class)->name('utilizadores');
        Route::get('/admin/encomendas', \App\Livewire\EncomendasAdminComponent::class)->name('admin.encomendas');
        Route::get('/logs', \App\Livewire\LogsComponent::class)->name('logs');
    });

});
