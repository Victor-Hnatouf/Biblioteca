<?php
namespace App\Livewire;
use App\Models\Encomenda;
use App\Services\LivroVendaService;
use Livewire\Component;
class EncomendasAdminComponent extends Component
{
    public $search = '';
    public $filterEstado = 'todas'; 
    public $expandedOrderId = null;
    public function render()
    {
        abort_unless(auth()->user()?->isAdmin(), 403, 'Acesso reservado aos guardiões do templo.');
        $encomendas = Encomenda::query()
            ->with(['user', 'items'])
            ->when($this->search, function ($q) {
                $q->where('id', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function ($uq) {
                      $uq->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                  });
            })
            ->when($this->filterEstado !== 'todas', function ($q) {
                $q->where('estado', $this->filterEstado);
            })
            ->orderByDesc('created_at')
            ->get();
        return view('livewire.encomendas-admin-component', [
            'encomendas' => $encomendas,
        ])->layout('layouts.app');
    }
    public function toggleExpandOrder(int $orderId): void
    {
        if ($this->expandedOrderId === $orderId) {
            $this->expandedOrderId = null;
        } else {
            $this->expandedOrderId = $orderId;
        }
    }
    public function alterarEstado(int $orderId, string $novoEstado): void
    {
        $encomenda = Encomenda::with('items')->findOrFail($orderId);
        if (!in_array($novoEstado, [Encomenda::ESTADO_PENDENTE, Encomenda::ESTADO_PAGA])) {
            return;
        }
        $eraPaga = $encomenda->estado === Encomenda::ESTADO_PAGA;
        $encomenda->update(['estado' => $novoEstado]);
        if ($novoEstado === Encomenda::ESTADO_PAGA && !$eraPaga) {
            LivroVendaService::marcarLivrosDaEncomendaComoVendidos($encomenda);
        }
        session()->flash('message', "Encomenda #{$orderId} atualizada com sucesso para: " . ($novoEstado === 'paga' ? 'Paga' : 'Pendente'));
    }
}
