<?php

namespace App\Console\Commands;

use App\Mail\RequisicaoLembrete;
use App\Models\Requisicao;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EnviarLembretesRequisicoes extends Command
{
    protected $signature = 'requisicoes:lembretes';
    protected $description = 'Envia emails de lembrete no dia anterior à entrega prevista.';

    public function handle(): int
    {
        $amanha = Carbon::tomorrow()->toDateString();

        $requisicoes = Requisicao::query()
            ->with(['livro'])
            ->whereDate('previsto_entrega_em', $amanha)
            ->whereNull('entregue_em')
            ->whereNull('cidadao_entregou_em')
            ->get();

        $enviados = 0;

        foreach ($requisicoes as $r) {
            if (!$r->cidadao_email) {
                continue;
            }
            Mail::to($r->cidadao_email)->send(new RequisicaoLembrete($r));
            $enviados++;
        }

        $this->info("Lembretes enviados: {$enviados}");

        return self::SUCCESS;
    }
}

