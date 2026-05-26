<?php

namespace App\Livewire;

use App\Models\AlertaDisponibilidade;
use App\Models\Livro;
use App\Models\Review;
use App\Services\BookSimilarityService;
use Livewire\Component;

class CatalogoComponent extends Component
{
    public $search = '';

    public $selectedLivroId = null;
    public $isDetailModalOpen = false;

    // Alert functionality
    public $alertaLivroId = null;
    public $jaTemAlerta = false;

    public function render()
    {
        $livros = Livro::query()
            ->with(['editora', 'autores', 'requisicaoAtiva', 'reviewsAtivos'])
            ->when($this->search, function ($q) {
                $q->where('nome', 'like', '%' . $this->search . '%')
                    ->orWhere('isbn', 'like', '%' . $this->search . '%');
            })
            ->orderBy('nome')
            ->get();

        $selectedLivro = null;
        $relatedLivros = [];
        
        if ($this->selectedLivroId) {
            $selectedLivro = Livro::with(['editora', 'autores', 'reviewsAtivos' => function($q) {
                $q->orderByDesc('created_at');
            }])->find($this->selectedLivroId);
            
            if ($selectedLivro) {
                $relatedLivros = $this->getRelatedLivros($selectedLivro);
            }
        }

        return view('livewire.catalogo-component', [
            'livros' => $livros,
            'selectedLivro' => $selectedLivro,
            'relatedLivros' => $relatedLivros,
        ])->layout('layouts.app');
    }

    public function openLivroDetail(int $livroId): void
    {
        $this->selectedLivroId = $livroId;
        $this->isDetailModalOpen = true;
    }

    public function closeLivroDetail(): void
    {
        $this->selectedLivroId = null;
        $this->isDetailModalOpen = false;
    }

    public function solicitarAlerta(int $livroId): void
    {
        $user = auth()->user();
        abort_unless($user, 403);

        // Verificar se o cidadão já tem um alerta ativo para este livro
        $jaAlerta = AlertaDisponibilidade::query()
            ->where('livro_id', $livroId)
            ->where('cidadao_id', $user->id)
            ->where('notificado', false)
            ->exists();

        if ($jaAlerta) {
            session()->flash('message', 'Já tens um alerta ativo para este livro.');
            return;
        }

        AlertaDisponibilidade::create([
            'livro_id' => $livroId,
            'cidadao_id' => $user->id,
            'cidadao_nome' => $user->name,
            'cidadao_email' => $user->email,
            'notificado' => false,
        ]);

        session()->flash('message', 'Alerta criado com sucesso. Serás notificado quando o livro estiver disponível.');
    }

    public function cancelarAlerta(int $livroId): void
    {
        $user = auth()->user();
        abort_unless($user, 403);

        AlertaDisponibilidade::query()
            ->where('livro_id', $livroId)
            ->where('cidadao_id', $user->id)
            ->where('notificado', false)
            ->delete();

        session()->flash('message', 'Alerta cancelado com sucesso.');
    }

    private function getRelatedLivros(Livro $livro): array
    {
        return app(BookSimilarityService::class)->getRelatedBooks($livro, limit: 4);
    }
}

