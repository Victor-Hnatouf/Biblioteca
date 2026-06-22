<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
class Livro extends Model
{
    use HasFactory;
    protected $fillable = [
        'google_books_volume_id',
        'isbn',
        'nome',
        'editora_id',
        'bibliografia',
        'imagem_capa',
        'preco',
        'vendido_em',
    ];
    protected $casts = [
        'isbn' => 'encrypted',
        'nome' => 'encrypted',
        'bibliografia' => 'encrypted',
        'preco' => 'encrypted',
        'vendido_em' => 'datetime',
    ];
    public function scopeDisponivelNoCatalogo($query)
    {
        return $query->whereNull('vendido_em');
    }
    public function isVendido(): bool
    {
        return $this->vendido_em !== null;
    }
    public function temPrecoVenda(): bool
    {
        return $this->preco !== null && $this->preco !== '' && (float) $this->preco > 0;
    }
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
    public function carrinhoItems(): HasMany
    {
        return $this->hasMany(CarrinhoItem::class);
    }
}
