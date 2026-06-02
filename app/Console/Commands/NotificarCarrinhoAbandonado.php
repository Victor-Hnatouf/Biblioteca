<?php

namespace App\Console\Commands;

use App\Mail\CarrinhoAbandonadoEmail;
use App\Models\CarrinhoItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NotificarCarrinhoAbandonado extends Command
{
    protected $signature = 'carrinho:notificar-abandonado {--force : Ignora a barreira de 1 hora e notifica imediatamente}';
    protected $description = 'Envia uma notificação por email para os cidadãos que adicionaram livros ao carrinho mas não concluíram a compra após 1 hora.';

    public function handle(): int
    {
        $limiteTempo = $this->option('force') ? Carbon::now() : Carbon::now()->subHour();

        
        
        $itensAbandonados = CarrinhoItem::query()
            ->with(['livro', 'user'])
            ->where('abandoned_email_sent', false)
            ->where('updated_at', '<=', $limiteTempo)
            ->get()
            ->groupBy('user_id');

        $notificadosCount = 0;

        foreach ($itensAbandonados as $userId => $items) {
            $user = $items->first()->user;

            if (!$user || !$user->email) {
                continue;
            }

            try {
                
                Mail::to($user->email)->send(new CarrinhoAbandonadoEmail($items));

                
                CarrinhoItem::where('user_id', $userId)
                    ->whereIn('id', $items->pluck('id'))
                    ->update(['abandoned_email_sent' => true]);

                $this->info("Notificação de carrinho abandonado enviada com sucesso para: {$user->name} ({$user->email})");
                $notificadosCount++;
            } catch (\Exception $e) {
                $this->error("Erro ao enviar email para {$user->email}: " . $e->getMessage());
            }
        }

        $this->info("Total de cidadãos notificados: {$notificadosCount}");

        return self::SUCCESS;
    }
}
