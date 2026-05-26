<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    public const ESTADO_SUSPENSO = 'suspenso';
    public const ESTADO_ATIVO = 'ativo';
    public const ESTADO_RECUSADO = 'recusado';

    protected $fillable = [
        'livro_id',
        'cidadao_id',
        'cidadao_nome',
        'cidadao_email',
        'cidadao_profile_photo_path',
        'comentario',
        'classificacao',
        'estado',
        'justificacao_recusa',
        'aprovado_por_admin_id',
        'aprovado_em',
    ];

    protected $casts = [
        'classificacao' => 'integer',
        'aprovado_em' => 'datetime',
    ];

    public function livro(): BelongsTo
    {
        return $this->belongsTo(Livro::class);
    }

    public function cidadao(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cidadao_id');
    }

    public function aprovadoPorAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprovado_por_admin_id');
    }

    public function isAtivo(): bool
    {
        return $this->estado === self::ESTADO_ATIVO;
    }

    public function isSuspenso(): bool
    {
        return $this->estado === self::ESTADO_SUSPENSO;
    }

    public function isRecusado(): bool
    {
        return $this->estado === self::ESTADO_RECUSADO;
    }

    public static function opcoesEstado(): array
    {
        return [
            self::ESTADO_SUSPENSO => 'Suspenso',
            self::ESTADO_ATIVO => 'Ativo',
            self::ESTADO_RECUSADO => 'Recusado',
        ];
    }
}
