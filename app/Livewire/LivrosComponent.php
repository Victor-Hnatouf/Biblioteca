<?php

namespace App\Livewire;

use App\Exports\LivrosExport;
use App\Mail\LivroDisponivel;
use App\Models\AlertaDisponibilidade;
use App\Models\Autor;
use App\Models\Editora;
use App\Models\Livro;
use App\Models\Requisicao;
use App\Services\GoogleBooksService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class LivrosComponent extends Component
{
    use WithFileUploads;

    public $search = '';

    public $sortField = 'id';

    public $sortAsc = true;

    public $page = 1;

    public $livro_id;

    public $isbn;

    public $nome;

    public $editora_id;

    public $bibliografia;

    public $imagem_capa;

    public $new_imagem_capa;

    public $preco;

    public $autores_selecionados = [];

    public $isModalOpen = false;

    public $historico_requisicoes = [];

    public bool $googlePanelOpen = false;

    public string $googleQuery = '';

    public array $googleResults = [];

    public int $googleTotal = 0;

    public ?int $googleNextStart = null;

    public bool $googleSearching = false;

    public ?string $googleMessage = null;

    public bool $googleConfigured = false;

    public function mount(GoogleBooksService $googleBooks): void
    {
        $this->googleConfigured = $googleBooks->isConfigured();
    }

    public function render()
    {
        $all = Livro::with(['editora', 'autores'])->get();

        if ($this->search) {
            $all = $all->filter(function ($item) {
                return stripos($item->nome, $this->search) !== false || stripos($item->isbn, $this->search) !== false;
            });
        }

        if ($this->sortAsc) {
            $all = $all->sortBy($this->sortField);
        } else {
            $all = $all->sortByDesc($this->sortField);
        }

        $perPage = 10;
        $items = $all->forPage($this->page, $perPage);
        $livros = new LengthAwarePaginator($items, $all->count(), $perPage, $this->page);

        return view('livewire.livros-component', [
            'livros' => $livros,
            'editoras_list' => Editora::all(),
            'autores_list' => Autor::all(),
        ])->layout('layouts.app');
    }

    public function openGoogleImport(GoogleBooksService $googleBooks): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $this->googleConfigured = $googleBooks->isConfigured();
        $this->googlePanelOpen = true;
        $this->googleQuery = '';
        $this->googleResults = [];
        $this->googleTotal = 0;
        $this->googleNextStart = null;
        $this->googleMessage = null;
    }

    public function closeGoogleImport(): void
    {
        $this->googlePanelOpen = false;
        $this->googleSearching = false;
    }

    public function searchGoogleBooks(GoogleBooksService $googleBooks): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $this->googleMessage = null;
        $this->validate([
            'googleQuery' => 'required|string|min:2|max:200',
        ]);

        $this->googleSearching = true;
        try {
            $payload = $googleBooks->searchVolumes(trim($this->googleQuery), 0);
            $this->googleResults = $payload['items'];
            $this->googleTotal = $payload['total'];
            $this->googleNextStart = $payload['next_start'];
            if ($this->googleResults === []) {
                $this->googleMessage = 'Nenhum volume encontrado para esta pesquisa.';
            }
        } catch (\Throwable $e) {
            $this->googleResults = [];
            $this->googleTotal = 0;
            $this->googleNextStart = null;
            $this->googleMessage = $e->getMessage();
        } finally {
            $this->googleSearching = false;
        }
    }

    public function loadMoreGoogleBooks(GoogleBooksService $googleBooks): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        if ($this->googleNextStart === null) {
            return;
        }

        $this->googleSearching = true;
        $this->googleMessage = null;
        try {
            $payload = $googleBooks->searchVolumes(trim($this->googleQuery), $this->googleNextStart);
            $this->googleResults = array_merge($this->googleResults, $payload['items']);
            $this->googleTotal = $payload['total'];
            $this->googleNextStart = $payload['next_start'];
        } catch (\Throwable $e) {
            $this->googleMessage = $e->getMessage();
        } finally {
            $this->googleSearching = false;
        }
    }

    public function importGoogleVolume(string $volumeId, GoogleBooksService $googleBooks): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $volumeId = trim($volumeId);
        if ($volumeId === '') {
            return;
        }

        $this->googleMessage = null;
        try {
            $googleBooks->importVolumeById($volumeId);
            session()->flash('message', 'Tomo importado da Google Books e gravado no acervo.');
            $this->closeGoogleImport();
        } catch (\Throwable $e) {
            $this->googleMessage = $e->getMessage();
        }
    }

    public function setPage($page)
    {
        $this->page = $page;
    }

    public function previousPage()
    {
        $this->page--;
    }

    public function nextPage()
    {
        $this->page++;
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortAsc = ! $this->sortAsc;
        } else {
            $this->sortAsc = true;
        }
        $this->sortField = $field;
    }

    public function create()
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $this->resetCreateForm();
        $this->openModalPopover();
    }

    public function generateISBN()
    {
        $prefix = '978';
        $group = rand(0, 9);
        $publisher = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $title = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

        $isbn = $prefix.'-'.$group.'-'.$publisher.'-'.$title.'-'.rand(0, 9);
        $this->isbn = $isbn;
    }

    public function openModalPopover()
    {
        $this->isModalOpen = true;
    }

    public function closeModalPopover()
    {
        $this->isModalOpen = false;
    }

    private function mapRequisicoesParaHistorico($requisicoes): array
    {
        return $requisicoes->map(function ($r) {
            return [
                'numero' => $r->numero,
                'cidadao_nome' => $r->cidadao_nome,
                'cidadao_email' => $r->cidadao_email,
                'requisitado_em' => optional($r->requisitado_em)->format('d/m/Y H:i'),
                'previsto_entrega_em' => optional($r->previsto_entrega_em)->format('d/m/Y'),
                'cidadao_entregou_em' => optional($r->cidadao_entregou_em)->format('d/m/Y H:i'),
                'entregue_em' => $r->entregue_em ? optional($r->entregue_em)->format('d/m/Y') : null,
                'condicao_na_devolucao' => Requisicao::labelCondicao($r->condicao_na_devolucao),
                'dias_decorridos' => $r->dias_decorridos,
            ];
        })->values()->all();
    }

    private function resetCreateForm()
    {
        $this->livro_id = null;
        $this->isbn = '';
        $this->nome = '';
        $this->editora_id = '';
        $this->bibliografia = '';
        $this->imagem_capa = null;
        $this->new_imagem_capa = null;
        $this->preco = '';
        $this->autores_selecionados = [];
        $this->historico_requisicoes = [];
    }

    public function store()
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $this->validate([
            'isbn' => 'required',
            'nome' => 'required',
            'editora_id' => 'required|exists:editoras,id',
            'preco' => 'nullable|numeric',
            'new_imagem_capa' => 'nullable|image|max:2048',
        ]);

        $path = $this->imagem_capa;
        if ($this->new_imagem_capa) {
            $path = $this->new_imagem_capa->store('capas', 'public');
        }

        $livro = Livro::updateOrCreate(['id' => $this->livro_id], [
            'isbn' => $this->isbn,
            'nome' => $this->nome,
            'editora_id' => $this->editora_id,
            'bibliografia' => $this->bibliografia,
            'imagem_capa' => $path,
            'preco' => $this->preco,
        ]);

        $livro->autores()->sync($this->autores_selecionados);

        
        
        if (!$this->livro_id) {
            $this->enviarNotificacoesDisponibilidade($livro);
        }

        session()->flash('message', $this->livro_id ? 'Livro atualizado com sucesso.' : 'Livro criado com sucesso.');
        $this->closeModalPopover();
        $this->resetCreateForm();
    }

    private function enviarNotificacoesDisponibilidade(Livro $livro): void
    {
        $alertasPendentes = $livro->alertasPendentes;

        foreach ($alertasPendentes as $alerta) {
            try {
                Mail::to($alerta->cidadao_email)->send(new LivroDisponivel($livro, $alerta));
                
                
                $alerta->update([
                    'notificado' => true,
                    'notificado_em' => now(),
                ]);
            } catch (\Exception $e) {
                
                \Log::error('Erro ao enviar notificação de disponibilidade: ' . $e->getMessage());
            }
        }
    }

    public function edit($id)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $livro = Livro::with(['autores', 'requisicoes' => function ($q) {
            $q->with('cidadao')->orderByDesc('requisitado_em')->orderByDesc('id');
        }])->findOrFail($id);
        $this->livro_id = $id;
        $this->isbn = $livro->isbn;
        $this->nome = $livro->nome;
        $this->editora_id = $livro->editora_id;
        $this->bibliografia = $livro->bibliografia;
        $this->imagem_capa = $livro->imagem_capa;
        $this->preco = $livro->preco;
        $this->autores_selecionados = $livro->autores->pluck('id')->toArray();
        $this->historico_requisicoes = $this->mapRequisicoesParaHistorico($livro->requisicoes);
        $this->openModalPopover();
    }

    public function delete($id)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        Livro::find($id)->delete();
        session()->flash('message', 'Livro apagado com sucesso.');
    }

    public function exportExcel()
    {
        return Excel::download(new LivrosExport, 'livros.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
}
