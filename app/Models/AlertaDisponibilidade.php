<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class AlertaDisponibilidade extends Model
{
    protected $fillable = [
        'livro_id',
        'cidadao_id',
        'cidadao_nome',
        'cidadao_email',
        'notificado',
        'notificado_em',
    ];
    protected $casts = [
        'notificado' => 'boolean',
        'notificado_em' => 'datetime',
    ];
    public function livro(): BelongsTo
    {
        return $this->belongsTo(Livro::class);
    }
    public function cidadao(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cidadao_id');
    }
    public function isNotificado(): bool
    {
        return $this->notificado;
    }
    public function isPendente(): bool
    {
        return !$this->notificado;
    }
}
