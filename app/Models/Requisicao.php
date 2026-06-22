<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
class Requisicao extends Model
{
    use HasFactory;
    public const CONDICAO_BOAS = 'boas';
    public const CONDICAO_MEDIANAS = 'medianas';
    public const CONDICAO_MAS = 'mas';
    public static function opcoesCondicaoDevolucao(): array
    {
        return [
            self::CONDICAO_BOAS => 'Boas condições',
            self::CONDICAO_MEDIANAS => 'Condições medianas',
            self::CONDICAO_MAS => 'Más condições',
        ];
    }
    public static function labelCondicao(?string $condicao): ?string
    {
        if ($condicao === null) {
            return null;
        }
        return self::opcoesCondicaoDevolucao()[$condicao] ?? $condicao;
    }
    protected $table = 'requisicoes';
    protected $fillable = [
        'numero',
        'livro_id',
        'cidadao_id',
        'cidadao_nome',
        'cidadao_email',
        'cidadao_profile_photo_path',
        'requisitado_em',
        'previsto_entrega_em',
        'cidadao_entregou_em',
        'entregue_em',
        'condicao_na_devolucao',
        'confirmado_por_admin_id',
        'dias_decorridos',
    ];
    protected $casts = [
        'requisitado_em' => 'datetime',
        'previsto_entrega_em' => 'date',
        'cidadao_entregou_em' => 'datetime',
        'entregue_em' => 'date',
        'dias_decorridos' => 'integer',
    ];
    public function livro(): BelongsTo
    {
        return $this->belongsTo(Livro::class);
    }
    public function cidadao(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cidadao_id');
    }
    public function confirmadoPorAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmado_por_admin_id');
    }
    public function isAtiva(): bool
    {
        return $this->entregue_em === null;
    }
    public function aguardaRelatorioBiblioteca(): bool
    {
        return $this->entregue_em === null
            && $this->cidadao_entregou_em !== null;
    }
    public function diasDecorridosAteEntrega(): ?int
    {
        if (!$this->requisitado_em || !$this->entregue_em) {
            return null;
        }
        $inicio = Carbon::parse($this->requisitado_em)->startOfDay();
        $fim = Carbon::parse($this->entregue_em)->startOfDay();
        return $inicio->diffInDays($fim);
    }
}
