<?php
namespace App\Providers;
use App\Models\Autor;
use App\Models\Editora;
use App\Models\Encomenda;
use App\Models\EncomendaItem;
use App\Models\Livro;
use App\Models\Requisicao;
use App\Models\Review;
use App\Models\User;
use App\Observers\ActivityLogObserver;
use Illuminate\Support\ServiceProvider;
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }
    public function boot(): void
    {
        $modelos = [
            Requisicao::class,
            Livro::class,
            Editora::class,
            Autor::class,
            User::class,
            Encomenda::class,
            EncomendaItem::class,
            Review::class,
        ];
        foreach ($modelos as $modelo) {
            $modelo::observe(ActivityLogObserver::class);
        }
    }
}
