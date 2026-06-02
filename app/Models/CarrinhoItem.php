<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarrinhoItem extends Model
{
    protected $table = 'carrinho_items';

    protected $fillable = [
        'user_id',
        'livro_id',
        'quantidade',
        'abandoned_email_sent',
    ];

    protected $casts = [
        'abandoned_email_sent' => 'boolean',
        'quantidade' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function livro(): BelongsTo
    {
        return $this->belongsTo(Livro::class);
    }
}
