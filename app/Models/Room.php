<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Room extends Model
{
    use HasFactory;
    protected $fillable = [
        'nome',
        'avatar_path',
        'is_dm',
        'is_group',
        'is_admin_only',
        'created_by',
    ];
    protected $casts = [
        'is_dm' => 'boolean',
        'is_group' => 'boolean',
        'is_admin_only' => 'boolean',
    ];
    public function isChannel(): bool
    {
        return ! $this->is_dm && ! $this->is_group;
    }
    public function isGroup(): bool
    {
        return $this->is_group && ! $this->is_dm;
    }
    public function scopeChannels($query)
    {
        return $query->where('is_dm', false)->where('is_group', false);
    }
    public function scopeGroups($query)
    {
        return $query->where('is_group', true)->where('is_dm', false);
    }
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'room_user');
    }
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar_path) {
            return asset('storage/' . $this->avatar_path);
        }
        $name = urlencode($this->nome ?? 'Chat');
        return "https://ui-avatars.com/api/?name={$name}&color=7F9CF5&background=EBF4FF";
    }
}
