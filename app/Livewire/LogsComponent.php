<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use Livewire\Component;
use Livewire\WithPagination;

class LogsComponent extends Component
{
    use WithPagination;

    public string $filtroModulo = '';
    public string $filtroUtilizador = '';
    public string $filtroEvento = '';
    public string $filtroDataInicio = '';
    public string $filtroDataFim = '';
    public string $filtroIp = '';

    protected $queryString = [
        'filtroModulo'     => ['except' => ''],
        'filtroUtilizador' => ['except' => ''],
        'filtroEvento'     => ['except' => ''],
        'filtroDataInicio' => ['except' => ''],
        'filtroDataFim'    => ['except' => ''],
        'filtroIp'         => ['except' => ''],
    ];

    public function updatingFiltroModulo(): void     { $this->resetPage(); }
    public function updatingFiltroUtilizador(): void { $this->resetPage(); }
    public function updatingFiltroEvento(): void     { $this->resetPage(); }
    public function updatingFiltroDataInicio(): void { $this->resetPage(); }
    public function updatingFiltroDataFim(): void    { $this->resetPage(); }
    public function updatingFiltroIp(): void         { $this->resetPage(); }

    public function limparFiltros(): void
    {
        $this->reset([
            'filtroModulo',
            'filtroUtilizador',
            'filtroEvento',
            'filtroDataInicio',
            'filtroDataFim',
            'filtroIp',
        ]);
        $this->resetPage();
    }

    public function render()
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $query = ActivityLog::query()
            ->with('user')
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if ($this->filtroModulo !== '') {
            $query->where('modulo', $this->filtroModulo);
        }

        if ($this->filtroEvento !== '') {
            $query->where('evento', $this->filtroEvento);
        }

        if ($this->filtroUtilizador !== '') {
            $query->where(function ($q) {
                $q->where('user_nome', 'like', '%' . $this->filtroUtilizador . '%')
                  ->orWhere('user_email', 'like', '%' . $this->filtroUtilizador . '%');
            });
        }

        if ($this->filtroIp !== '') {
            $query->where('ip', 'like', '%' . $this->filtroIp . '%');
        }

        if ($this->filtroDataInicio !== '') {
            $query->whereDate('created_at', '>=', $this->filtroDataInicio);
        }

        if ($this->filtroDataFim !== '') {
            $query->whereDate('created_at', '<=', $this->filtroDataFim);
        }

        $logs = $query->paginate(25);

        
        $modulos = ActivityLog::query()
            ->select('modulo')
            ->distinct()
            ->orderBy('modulo')
            ->pluck('modulo');

        return view('livewire.logs-component', [
            'logs'    => $logs,
            'modulos' => $modulos,
        ])->layout('layouts.app');
    }
}
