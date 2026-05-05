<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Livro extends Model
{
    protected $fillable = ['isbn', 'nome', 'editora_id', 'bibliografia', 'imagem_capa', 'preco'];

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
}
