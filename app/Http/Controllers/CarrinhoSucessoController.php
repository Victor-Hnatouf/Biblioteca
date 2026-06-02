<?php

namespace App\Http\Controllers;

use App\Models\CarrinhoItem;
use App\Models\Encomenda;
use App\Services\LivroVendaService;
use Illuminate\Support\Facades\DB;
use Stripe\StripeClient;

class CarrinhoSucessoController extends Controller
{
    public function sucesso($id)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $encomenda = Encomenda::with('items')->where('user_id', $user->id)->findOrFail($id);

        $stripeSecret = config('services.stripe.secret');
        if ($encomenda->stripe_session_id && !empty($stripeSecret) && class_exists(StripeClient::class)) {
            try {
                $stripe = new StripeClient($stripeSecret);
                $session = $stripe->checkout->sessions->retrieve($encomenda->stripe_session_id);
                if ($session->payment_status !== 'paid') {
                    return redirect()->route('carrinho.cancelado', ['id' => $encomenda->id]);
                }
            } catch (\Exception) {
                return redirect()->route('carrinho.cancelado', ['id' => $encomenda->id])
                    ->with('error', 'Não foi possível confirmar o pagamento junto do Stripe.');
            }
        }

        if ($encomenda->estado === Encomenda::ESTADO_PENDENTE) {
            DB::transaction(function () use ($encomenda, $user) {
                $encomenda->update([
                    'estado' => Encomenda::ESTADO_PAGA,
                ]);

                LivroVendaService::marcarLivrosDaEncomendaComoVendidos($encomenda);

                CarrinhoItem::where('user_id', $user->id)->delete();
            });
        }

        return view('carrinho.sucesso', [
            'encomenda' => $encomenda,
        ]);
    }
}
