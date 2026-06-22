<?php
namespace App\Models;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'estado',
        'chat_nickname',
        'chat_photo_path',
    ];
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];
    protected $appends = [
        'profile_photo_url',
    ];
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public const ROLE_ADMIN = 'admin';
    public const ROLE_CIDADAO = 'cidadao';
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }
    public function isCidadao(): bool
    {
        return $this->role === self::ROLE_CIDADAO;
    }
    public function requisicoes(): HasMany
    {
        return $this->hasMany(Requisicao::class, 'cidadao_id');
    }
    public function carrinhoItems(): HasMany
    {
        return $this->hasMany(CarrinhoItem::class);
    }
    public function encomendas(): HasMany
    {
        return $this->hasMany(Encomenda::class);
    }
    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class, 'room_user');
    }
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
    public function getAvatarAttribute()
    {
        return $this->profile_photo_url;
    }
    public function getChatDisplayNameAttribute(): string
    {
        return $this->chat_nickname ?: $this->name;
    }
    public function getChatAvatarUrlAttribute(): string
    {
        if ($this->chat_photo_path) {
            return asset('storage/' . $this->chat_photo_path);
        }
        return $this->profile_photo_url;
    }
}
