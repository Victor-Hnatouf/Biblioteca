<?php
namespace App\Livewire;
use App\Models\CarrinhoItem;
use App\Models\Encomenda;
use App\Models\EncomendaItem;
use App\Models\Livro;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Stripe\StripeClient;
class CarrinhoComponent extends Component
{
    public $step = 1; 
    public $morada = '';
    protected $listeners = ['cart-updated' => '$refresh'];
    public function render()
    {
        $user = auth()->user();
        $cartItems = [];
        $total = 0.0;
        if ($user) {
            $cartItems = $user->carrinhoItems()
                ->with(['livro' => fn ($q) => $q->disponivelNoCatalogo()])
                ->get()
                ->filter(fn ($item) => $item->livro !== null);
            foreach ($cartItems as $item) {
                if ($item->livro && $item->livro->temPrecoVenda()) {
                    $total += (float) $item->livro->preco * $item->quantidade;
                }
            }
        }
        return view('livewire.carrinho-component', [
            'cartItems' => $cartItems,
            'total' => $total,
        ])->layout('layouts.app');
    }
    public function aumentarQuantidade(int $itemId): void
    {
        $item = CarrinhoItem::where('user_id', auth()->id())->find($itemId);
        if ($item) {
            $item->increment('quantidade');
            $item->update(['abandoned_email_sent' => false]);
            $this->dispatch('cart-updated');
        }
    }
    public function diminuirQuantidade(int $itemId): void
    {
        $item = CarrinhoItem::where('user_id', auth()->id())->find($itemId);
        if ($item) {
            if ($item->quantidade > 1) {
                $item->decrement('quantidade');
                $item->update(['abandoned_email_sent' => false]);
            } else {
                $item->delete();
            }
            $this->dispatch('cart-updated');
        }
    }
    public function removerItem(int $itemId): void
    {
        $item = CarrinhoItem::where('user_id', auth()->id())->find($itemId);
        if ($item) {
            $item->delete();
            $this->dispatch('cart-updated');
            session()->flash('message', 'Livro removido do carrinho.');
        }
    }
    public function avancarParaMorada(): void
    {
        $user = auth()->user();
        if (!$user || $user->carrinhoItems()->count() === 0) {
            session()->flash('error', 'O teu carrinho está vazio.');
            return;
        }
        $this->step = 2;
    }
    public function retroceder(int $targetStep): void
    {
        $this->step = $targetStep;
    }
    public function submitMorada(): void
    {
        $this->validate([
            'morada' => 'required|string|min:10',
        ], [
            'morada.required' => 'A morada de entrega é obrigatória.',
            'morada.min' => 'Por favor, insira uma morada detalhada (mínimo 10 caracteres).',
        ]);
        $this->step = 3;
    }
    public function pagarEncomenda(): void
    {
        $user = auth()->user();
        if (!$user || $user->carrinhoItems()->count() === 0) {
            session()->flash('error', 'O teu carrinho está vazio.');
            return;
        }
        $cartItems = $user->carrinhoItems()
            ->with(['livro' => fn ($q) => $q->disponivelNoCatalogo()])
            ->get()
            ->filter(fn ($item) => $item->livro !== null);
        foreach ($user->carrinhoItems()->whereDoesntHave('livro', fn ($q) => $q->disponivelNoCatalogo())->get() as $orphan) {
            $orphan->delete();
        }
        if ($cartItems->isEmpty()) {
            session()->flash('error', 'O teu carrinho está vazio ou contém livros já vendidos.');
            return;
        }
        $total = 0.0;
        foreach ($cartItems as $item) {
            if ($item->livro && $item->livro->temPrecoVenda()) {
                $total += (float) $item->livro->preco * $item->quantidade;
            }
        }
        $encomenda = DB::transaction(function () use ($user, $cartItems, $total) {
            $enc = Encomenda::create([
                'user_id' => $user->id,
                'morada' => $this->morada,
                'total' => $total,
                'estado' => Encomenda::ESTADO_PENDENTE,
            ]);
            foreach ($cartItems as $item) {
                EncomendaItem::create([
                    'encomenda_id' => $enc->id,
                    'livro_id' => $item->livro_id,
                    'nome_livro' => $item->livro->nome,
                    'preco_unitario' => (float) $item->livro->preco,
                    'quantidade' => $item->quantidade,
                ]);
            }
            return $enc;
        });
        $stripeSecret = config('services.stripe.secret');
        if (!empty($stripeSecret) && class_exists(StripeClient::class)) {
            try {
                $stripe = new StripeClient($stripeSecret);
                $checkoutSession = $stripe->checkout->sessions->create([
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'price_data' => [
                            'currency' => 'eur',
                            'product_data' => [
                                'name' => 'Encomenda #' . $encomenda->id . ' - Biblioteca de Alcantâra',
                                'description' => 'Compra de Livros Sagrados',
                            ],
                            'unit_amount' => (int) round($total * 100),
                        ],
                        'quantity' => 1,
                    ]],
                    'mode' => 'payment',
                    'success_url' => route('carrinho.sucesso', ['id' => $encomenda->id]),
                    'cancel_url' => route('carrinho.cancelado', ['id' => $encomenda->id]),
                ]);
                $encomenda->update([
                    'stripe_session_id' => $checkoutSession->id,
                ]);
                $this->redirect($checkoutSession->url);
                return;
            } catch (\Throwable $e) {
                session()->flash('stripe_error', 'Erro ao ligar ao Stripe: ' . $e->getMessage() . ' Podes usar a simulação local abaixo.');
            }
        } elseif (!empty($stripeSecret) && !class_exists(StripeClient::class)) {
            session()->flash('stripe_error', 'Pacote Stripe não carregado. Na raiz do projeto executa: composer install');
        }
        $this->dispatch('abrir-simulacao-local', encomendaId: $encomenda->id);
    }
    public function simularPagamentoSucesso(int $encomendaId): void
    {
        $this->redirect(route('carrinho.sucesso', ['id' => $encomendaId]));
    }
    public function simularPagamentoCancelado(int $encomendaId): void
    {
        $this->redirect(route('carrinho.cancelado', ['id' => $encomendaId]));
    }
}
