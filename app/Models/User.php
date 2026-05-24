<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Modules\Sossial\Models\Message as SosialMessage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'name',
        'email',
        'password',

        // ✅ Sosyal login alanları
        'provider',
        'provider_id',
        'avatar',
        'two_factor_required',
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'two_factor_required' => 'boolean',

            // ✅ Laravel 11 için doğru kullanım
            'password' => 'hashed',
        ];
    }

    /* -------------------------------------------------
     |  İLERİ SEVİYE GENİŞLETMEYE HAZIR ALAN
     |--------------------------------------------------
     */

    // ✅ Profil fotoğrafı helper (opsiyonel)
    public function avatarUrl(): string
    {
        if (!$this->avatar) {
            $initial = Str::upper(Str::substr($this->name ?: 'U', 0, 1));
            $svg = "<svg xmlns='http://www.w3.org/2000/svg' width='128' height='128' viewBox='0 0 128 128'>"
                . "<rect width='128' height='128' rx='64' fill='#dee2e6'/>"
                . "<text x='50%' y='54%' text-anchor='middle' dominant-baseline='middle' font-family='Arial, sans-serif' font-size='56' fill='#495057'>"
                . e($initial)
                . "</text></svg>";

            return 'data:image/svg+xml;charset=UTF-8,' . rawurlencode($svg);
        }

        if (Str::startsWith($this->avatar, ['http://', 'https://'])) {
            return $this->avatar;
        }

        return asset($this->avatar);
    }

    // ✅ Sosyal login mi?
    public function isSocialUser(): bool
    {
        return !is_null($this->provider);
    }

    public function sentSosialMessages(): HasMany
    {
        return $this->hasMany(SosialMessage::class, 'sender_id');
    }

    public function receivedSosialMessages(): HasMany
    {
        return $this->hasMany(SosialMessage::class, 'receiver_id');
    }
}
