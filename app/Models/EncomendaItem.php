<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class EncomendaItem extends Model
{
    protected $table = 'encomenda_items';
    protected $fillable = [
        'encomenda_id',
        'livro_id',
        'nome_livro',
        'preco_unitario',
        'quantidade',
    ];
    protected $casts = [
        'preco_unitario' => 'decimal:2',
        'quantidade' => 'integer',
    ];
    public function encomenda(): BelongsTo
    {
        return $this->belongsTo(Encomenda::class);
    }
    public function livro(): BelongsTo
    {
        return $this->belongsTo(Livro::class);
    }
}
