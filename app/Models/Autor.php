<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Autor extends Model
{
    protected $table = 'autores';
    protected $fillable = ['nome', 'foto'];

    protected $casts = [
        'nome' => 'encrypted',
    ];

    public function livros(): BelongsToMany
    {
        return $this->belongsToMany(Livro::class);
    }
}
