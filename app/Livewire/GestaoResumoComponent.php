<?php
namespace App\Livewire;
use App\Models\Encomenda;
use App\Models\Livro;
use App\Models\Requisicao;
use App\Models\User;
use Livewire\Component;
class GestaoResumoComponent extends Component
{
    public function render()
    {
        abort_unless(auth()->user()?->isAdmin(), 403, 'Acesso reservado aos guardiões do templo.');
        return view('livewire.gestao-resumo-component', [
            'totalLivros' => Livro::count(),
            'livrosNoCatalogo' => Livro::disponivelNoCatalogo()->count(),
            'livrosVendidos' => Livro::whereNotNull('vendido_em')->count(),
            'encomendasPendentes' => Encomenda::where('estado', Encomenda::ESTADO_PENDENTE)->count(),
            'encomendasPagas' => Encomenda::where('estado', Encomenda::ESTADO_PAGA)->count(),
            'requisicoesAtivas' => Requisicao::whereNull('entregue_em')->count(),
            'totalCidadaos' => User::where('role', User::ROLE_CIDADAO)->count(),
        ])->layout('layouts.app');
    }
}
