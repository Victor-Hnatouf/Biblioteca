<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Editora extends Model
{
    protected $fillable = ['nome', 'logotipo'];

    protected $casts = [
        'nome' => 'encrypted',
    ];

    public function livros(): HasMany
    {
        return $this->hasMany(Livro::class);
    }
}
