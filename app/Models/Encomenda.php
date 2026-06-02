<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Encomenda extends Model
{
    protected $table = 'encomendas';

    protected $fillable = [
        'user_id',
        'morada',
        'total',
        'estado',
        'stripe_session_id',
    ];

    public const ESTADO_PENDENTE = 'pendente';
    public const ESTADO_PAGA = 'paga';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(EncomendaItem::class);
    }
}
