<?php
namespace App\Livewire;
use App\Mail\ReviewAprovada;
use App\Mail\ReviewRecusada;
use App\Mail\ReviewSubmetida;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
class ReviewsComponent extends Component
{
    public $review_id;
    public $livro_id;
    public $comentario;
    public $classificacao = 5;
    public $justificacao_recusa;
    public $isModalOpen = false;
    public $isDetailModalOpen = false;
    public string $adminVista = 'suspenso';
    public $reviewDetail = null;
    public function render()
    {
        $user = auth()->user();
        if (! $user->isAdmin()) {
            $this->adminVista = 'minhas';
        } elseif (! in_array($this->adminVista, ['suspenso', 'ativo', 'recusado', 'todas'], true)) {
            $this->adminVista = 'suspenso';
        }
        $query = Review::query()->with(['livro', 'cidadao', 'aprovadoPorAdmin']);
        if ($user->isAdmin()) {
            if ($this->adminVista === 'suspenso') {
                $query->where('estado', Review::ESTADO_SUSPENSO);
            } elseif ($this->adminVista === 'ativo') {
                $query->where('estado', Review::ESTADO_ATIVO);
            } elseif ($this->adminVista === 'recusado') {
                $query->where('estado', Review::ESTADO_RECUSADO);
            }
            $reviews = $query->orderByDesc('created_at')->get();
        } else {
            $query->where('cidadao_id', $user->id);
            $reviews = $query->orderByDesc('created_at')->get();
        }
        $suspensoCount = $user->isAdmin() 
            ? Review::query()->where('estado', Review::ESTADO_SUSPENSO)->count() 
            : 0;
        return view('livewire.reviews-component', [
            'reviews' => $reviews,
            'suspensoCount' => $suspensoCount,
        ])->layout('layouts.app');
    }
    public function setAdminVista(string $vista): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        if (in_array($vista, ['suspenso', 'ativo', 'recusado', 'todas'], true)) {
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
        $this->reset(['livro_id', 'comentario', 'classificacao']);
    }
    public function openDetailModal(int $reviewId): void
    {
        $this->reviewDetail = Review::with(['livro', 'cidadao', 'aprovadoPorAdmin'])->findOrFail($reviewId);
        $this->justificacao_recusa = $this->reviewDetail->justificacao_recusa;
        $this->isDetailModalOpen = true;
    }
    public function closeDetailModal(): void
    {
        $this->isDetailModalOpen = false;
        $this->reviewDetail = null;
        $this->justificacao_recusa = null;
    }
    public function criarReview(): void
    {
        $user = auth()->user();
        $this->validate([
            'livro_id' => ['required', 'exists:livros,id'],
            'comentario' => ['required', 'string', 'min:10', 'max:1000'],
            'classificacao' => ['required', 'integer', 'min:1', 'max:5'],
        ]);
        $jaReview = Review::query()
            ->where('livro_id', $this->livro_id)
            ->where('cidadao_id', $user->id)
            ->exists();
        if ($jaReview) {
            $this->addError('livro_id', 'Já fizeste uma review para este livro.');
            return;
        }
        $requisitou = \App\Models\Requisicao::query()
            ->where('livro_id', $this->livro_id)
            ->where('cidadao_id', $user->id)
            ->whereNotNull('entregue_em')
            ->exists();
        if (!$requisitou) {
            $this->addError('livro_id', 'Só podes fazer review de livros que já requisitaste.');
            return;
        }
        $review = Review::create([
            'livro_id' => $this->livro_id,
            'cidadao_id' => $user->id,
            'cidadao_nome' => $user->name,
            'cidadao_email' => $user->email,
            'cidadao_profile_photo_path' => $user->profile_photo_path,
            'comentario' => $this->comentario,
            'classificacao' => $this->classificacao,
            'estado' => Review::ESTADO_SUSPENSO,
        ]);
        $admins = User::query()->where('role', User::ROLE_ADMIN)->pluck('email')->filter()->values()->all();
        if (!empty($admins)) {
            Mail::to($admins)->send(new ReviewSubmetida($review->fresh(['livro', 'cidadao'])));
        }
        session()->flash('message', 'Review submetida com sucesso. Aguarda aprovação por um admin.');
        $this->closeModal();
    }
    public function aprovarReview(int $reviewId): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $review = Review::query()->findOrFail($reviewId);
        if ($review->estado === Review::ESTADO_ATIVO) {
            return;
        }
        $review->update([
            'estado' => Review::ESTADO_ATIVO,
            'aprovado_por_admin_id' => auth()->id(),
            'aprovado_em' => now(),
            'justificacao_recusa' => null,
        ]);
        Mail::to($review->cidadao_email)->send(new ReviewAprovada($review->fresh(['livro'])));
        session()->flash('message', 'Review aprovada com sucesso.');
        $this->closeDetailModal();
    }
    public function recusarReview(int $reviewId): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $this->validate([
            'justificacao_recusa' => ['required', 'string', 'min:5', 'max:500'],
        ]);
        $review = Review::query()->findOrFail($reviewId);
        if ($review->estado === Review::ESTADO_RECUSADO) {
            return;
        }
        $review->update([
            'estado' => Review::ESTADO_RECUSADO,
            'aprovado_por_admin_id' => auth()->id(),
            'aprovado_em' => now(),
            'justificacao_recusa' => $this->justificacao_recusa,
        ]);
        Mail::to($review->cidadao_email)->send(new ReviewRecusada($review->fresh(['livro'])));
        session()->flash('message', 'Review recusada com sucesso.');
        $this->closeDetailModal();
    }
}
