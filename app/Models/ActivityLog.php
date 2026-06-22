<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ActivityLog extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'user_nome',
        'user_email',
        'modulo',
        'objeto_id',
        'evento',
        'alteracoes',
        'ip',
        'user_agent',
        'created_at',
    ];
    protected $casts = [
        'alteracoes' => 'array',
        'created_at' => 'datetime',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public static function registar(
        string $modulo,
        ?int $objetoId,
        string $evento,
        ?array $alteracoes = null,
    ): self {
        $user = null;
        try {
            $user = auth()->user();
        } catch (\Throwable) {
        }
        $userId = $user?->id;
        if ($userId && $modulo === 'User' && $objetoId === $userId && $evento === 'eliminado') {
            $userId = null;
        }
        $ip = null;
        $userAgent = null;
        try {
            if (app()->bound('request') && request()->ip()) {
                $ip = request()->ip();
                $userAgent = request()->userAgent();
            }
        } catch (\Throwable) {
        }
        return static::create([
            'user_id'    => $userId,
            'user_nome'  => $user?->name,
            'user_email' => $user?->email,
            'modulo'     => $modulo,
            'objeto_id'  => $objetoId,
            'evento'     => $evento,
            'alteracoes' => $alteracoes,
            'ip'         => $ip,
            'user_agent' => $userAgent,
            'created_at' => now(),
        ]);
    }
}
