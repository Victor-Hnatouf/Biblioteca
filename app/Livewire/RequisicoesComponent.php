<?php

namespace App\Livewire;

use App\Mail\RequisicaoConfirmacao;
use App\Mail\RequisicaoPendenteRelatorio;
use App\Models\Livro;
use App\Models\Requisicao;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class RequisicoesComponent extends Component
{
    public $livro_id = '';

    public $isModalOpen = false;

    public bool $historicoModalOpen = false;

    public string $historico_livro_nome = '';

    /** @var array<int, array<string, mixed>> */
    public array $historico_requisicoes = [];

    /** @var 'todas'|'por_relatar' */
    public string $adminVista = 'todas';

    public function mount(): void
    {
        $livro = request()->query('livro');
        if ($livro) {
            $this->livro_id = (string) $livro;
            $this->isModalOpen = true;
        }
    }

    public function render()
    {
        $user = auth()->user();

        if (! $user->isAdmin()) {
            $this->adminVista = 'todas';
        } elseif (! in_array($this->adminVista, ['todas', 'por_relatar'], true)) {
            $this->adminVista = 'todas';
        }

        $query = Requisicao::query()->with(['livro', 'cidadao']);

        if ($user->isAdmin()) {
            if ($this->adminVista === 'por_relatar') {
                $query->whereNotNull('cidadao_entregou_em')->whereNull('entregue_em');
                $requisicoes = $query->orderBy('cidadao_entregou_em')->get();
            } else {
                $requisicoes = $query->orderByDesc('id')->get();
            }
        } else {
            $query->where('cidadao_id', $user->id);
            $requisicoes = $query->orderByDesc('id')->get();
        }

        $requisicoesAtivas = Requisicao::query()
            ->whereNull('entregue_em')
            ->when(!$user->isAdmin(), fn ($q) => $q->where('cidadao_id', $user->id))
            ->count();

        $ultimos30Dias = Requisicao::query()
            ->where('requisitado_em', '>=', now()->subDays(30))
            ->when(!$user->isAdmin(), fn ($q) => $q->where('cidadao_id', $user->id))
            ->count();

        $entreguesHoje = Requisicao::query()
            ->whereDate('entregue_em', now()->toDateString())
            ->count();

        $porRelatarCount = $user->isAdmin()
            ? Requisicao::query()->whereNotNull('cidadao_entregou_em')->whereNull('entregue_em')->count()
            : 0;

        $livrosDisponiveis = Livro::query()
            ->with(['editora', 'autores'])
            ->whereDoesntHave('requisicaoAtiva')
            ->orderBy('nome')
            ->get();

        return view('livewire.requisicoes-component', [
            'requisicoes' => $requisicoes,
            'requisicoesAtivas' => $requisicoesAtivas,
            'ultimos30Dias' => $ultimos30Dias,
            'entreguesHoje' => $entreguesHoje,
            'porRelatarCount' => $porRelatarCount,
            'livrosDisponiveis' => $livrosDisponiveis,
        ])->layout('layouts.app');
    }

    /** @param 'todas'|'por_relatar' $vista */
    public function setAdminVista(string $vista): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        if (in_array($vista, ['todas', 'por_relatar'], true)) {
            $this->adminVista = $vista;
        }
    }

    public function openModal(): void
    {
        $this->isModalOpen = true;
    }

    public function closeModal(): void
    {
        $this->isModalOpen = false;
        $this->livro_id = '';
    }

    public function criarRequisicao(): void
    {
        $user = auth()->user();

        $this->validate([
            'livro_id' => ['required', 'exists:livros,id'],
        ]);

        // Regra: cidadão só pode ter 3 livros ativos
        $ativos = Requisicao::query()
            ->where('cidadao_id', $user->id)
            ->whereNull('entregue_em')
            ->count();

        if ($ativos >= 3) {
            $this->addError('livro_id', 'Atingiste o limite de 3 livros requisitados em simultâneo.');
            return;
        }

        $requisicao = DB::transaction(function () use ($user) {
            // Validar disponibilidade do livro (com lock simples para evitar corridas)
            $livro = Livro::query()->lockForUpdate()->findOrFail($this->livro_id);

            $jaAtivo = Requisicao::query()
                ->where('livro_id', $livro->id)
                ->whereNull('entregue_em')
                ->lockForUpdate()
                ->exists();

            if ($jaAtivo) {
                throw new \RuntimeException('Livro indisponível para requisição.');
            }

            $max = Requisicao::query()->lockForUpdate()->max('numero');
            $numero = ($max ?? 0) + 1;

            $requisitadoEm = now();
            $previsto = Carbon::parse($requisitadoEm)->addDays(5)->toDateString();

            return Requisicao::create([
                'numero' => $numero,
                'livro_id' => $livro->id,
                'cidadao_id' => $user->id,
                'cidadao_nome' => $user->name,
                'cidadao_email' => $user->email,
                'cidadao_profile_photo_path' => $user->profile_photo_path,
                'requisitado_em' => $requisitadoEm,
                'previsto_entrega_em' => $previsto,
            ]);
        });

        // Emails: Admins + Cidadão
        $admins = User::query()->where('role', User::ROLE_ADMIN)->pluck('email')->filter()->values()->all();
        if (!empty($admins)) {
            Mail::to($admins)->send(new RequisicaoConfirmacao($requisicao));
        }
        Mail::to($user->email)->send(new RequisicaoConfirmacao($requisicao));

        session()->flash('message', 'Requisição criada com sucesso (#' . $requisicao->numero . ').');
        $this->closeModal();
    }

    public function marcarEntregaNaBiblioteca(int $requisicaoId): void
    {
        $user = auth()->user();
        abort_unless($user, 403);

        $req = Requisicao::query()->findOrFail($requisicaoId);
        abort_unless($req->cidadao_id === $user->id, 403);

        if ($req->entregue_em !== null) {
            return;
        }
        if ($req->cidadao_entregou_em !== null) {
            session()->flash('message', 'Já registaste a devolução deste livro na biblioteca.');

            return;
        }

        $req->update(['cidadao_entregou_em' => now()]);

        $admins = User::query()->where('role', User::ROLE_ADMIN)->pluck('email')->filter()->values()->all();
        if (! empty($admins)) {
            Mail::to($admins)->send(new RequisicaoPendenteRelatorio($req->fresh(['livro', 'cidadao'])));
        }

        session()->flash('message', 'Registámos que entregaste o livro na biblioteca. A equipa foi notificada para relatar o estado do exemplar.');
    }

    public function registarRelatorioDevolucao(int $requisicaoId, string $condicao): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        if (! array_key_exists($condicao, Requisicao::opcoesCondicaoDevolucao())) {
            session()->flash('message', 'Condição inválida.');

            return;
        }

        $req = Requisicao::query()->with('livro')->findOrFail($requisicaoId);
        if ($req->entregue_em !== null) {
            return;
        }
        if ($req->cidadao_entregou_em === null) {
            session()->flash('message', 'O cidadão ainda não indicou a devolução na biblioteca.');

            return;
        }

        $entregue = now()->toDateString();
        $dias = $req->requisitado_em ? Carbon::parse($req->requisitado_em)->startOfDay()->diffInDays(Carbon::parse($entregue)->startOfDay()) : null;

        $req->update([
            'entregue_em' => $entregue,
            'condicao_na_devolucao' => $condicao,
            'confirmado_por_admin_id' => auth()->id(),
            'dias_decorridos' => $dias,
        ]);

        session()->flash('message', 'Relatório registado: requisição #' . $req->numero . ' concluída.');
    }

    public function openHistoricoLivro(int $livroId): void
    {
        $user = auth()->user();
        abort_unless($user, 403);

        $livro = Livro::query()->findOrFail($livroId);

        $query = Requisicao::query()
            ->where('livro_id', $livroId)
            ->with('cidadao')
            ->orderByDesc('requisitado_em')
            ->orderByDesc('id');

        if (!$user->isAdmin()) {
            $podeVer = Requisicao::query()
                ->where('livro_id', $livroId)
                ->where('cidadao_id', $user->id)
                ->exists();
            abort_unless($podeVer, 403);
            $query->where('cidadao_id', $user->id);
        }

        $this->historico_livro_nome = $livro->nome;
        $this->historico_requisicoes = $this->mapRequisicoesParaHistorico($query->get());
        $this->historicoModalOpen = true;
    }

    public function closeHistoricoModal(): void
    {
        $this->historicoModalOpen = false;
        $this->historico_livro_nome = '';
        $this->historico_requisicoes = [];
    }

    /**
     * @param \Illuminate\Support\Collection<int, Requisicao> $requisicoes
     * @return array<int, array<string, mixed>>
     */
    private function mapRequisicoesParaHistorico($requisicoes): array
    {
        return $requisicoes->map(function (Requisicao $r) {
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
}

