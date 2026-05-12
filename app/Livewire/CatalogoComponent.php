<?php

namespace App\Livewire;

use App\Models\Livro;
use Livewire\Component;

class CatalogoComponent extends Component
{
    public $search = '';

    public function render()
    {
        $livros = Livro::query()
            ->with(['editora', 'autores', 'requisicaoAtiva'])
            ->when($this->search, function ($q) {
                $q->where('nome', 'like', '%' . $this->search . '%')
                    ->orWhere('isbn', 'like', '%' . $this->search . '%');
            })
            ->orderBy('nome')
            ->get();

        return view('livewire.catalogo-component', [
            'livros' => $livros,
        ])->layout('layouts.app');
    }
}

