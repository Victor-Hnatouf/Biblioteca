<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Livro extends Model
{
    protected $fillable = ['google_books_volume_id', 'isbn', 'nome', 'editora_id', 'bibliografia', 'imagem_capa', 'preco'];

    protected $casts = [
        'isbn' => 'encrypted',
        'nome' => 'encrypted',
        'bibliografia' => 'encrypted',
        'preco' => 'encrypted',
    ];

    public function editora(): BelongsTo
    {
        return $this->belongsTo(Editora::class);
    }

    public function autores(): BelongsToMany
    {
        return $this->belongsToMany(Autor::class);
    }

    public function requisicoes(): HasMany
    {
        return $this->hasMany(Requisicao::class);
    }

    public function requisicaoAtiva(): HasOne
    {
        return $this->hasOne(Requisicao::class)->whereNull('entregue_em');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function reviewsAtivos(): HasMany
    {
        return $this->hasMany(Review::class)->where('estado', Review::ESTADO_ATIVO);
    }

    public function alertasDisponibilidade(): HasMany
    {
        return $this->hasMany(AlertaDisponibilidade::class);
    }

    public function alertasPendentes(): HasMany
    {
        return $this->hasMany(AlertaDisponibilidade::class)->where('notificado', false);
    }
}
