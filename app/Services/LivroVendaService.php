<?php

namespace App\Services;

use App\Models\CarrinhoItem;
use App\Models\Encomenda;
use App\Models\Livro;
use Illuminate\Support\Facades\DB;

class LivroVendaService
{
    public static function marcarLivrosDaEncomendaComoVendidos(Encomenda $encomenda): void
    {
        $encomenda->loadMissing('items');

        DB::transaction(function () use ($encomenda) {
            foreach ($encomenda->items as $item) {
                if (!$item->livro_id) {
                    continue;
                }

                Livro::whereKey($item->livro_id)
                    ->whereNull('vendido_em')
                    ->update(['vendido_em' => now()]);

                CarrinhoItem::where('livro_id', $item->livro_id)->delete();
            }
        });
    }
}
