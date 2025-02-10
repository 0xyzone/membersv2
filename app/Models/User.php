<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;
use App\Models\Social;
use App\Models\Document;
use App\Models\UserTeam;
use Illuminate\Support\Str;
use App\Models\UserGameInfo;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;

class User extends Authenticatable implements FilamentUser, HasAvatar
{

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    // Filament Setup
    public function getFilamentAvatarUrl(): ?string
    {
        if(!$this->avatar_url){
            return null;
        }
        return asset('storage/' . $this->avatar_url);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return true;
        }

        return true;
    }
    // Filament Setup End

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    // public function getAvatarUrlAttribute(): string
    // {
    //     return $this->attributes['avatar_url']
    //         ? asset('storage/' . $this->attributes['avatar_url'])
    //         : null; // Default avatar
    // }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->user_id = Str::uuid();
        });
    }

    /**
     * Get all of the socials for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function socials(): HasMany
    {
        return $this->hasMany(Social::class);
    }

    /**
     * Get all of the documents for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get all of the userGameInfos for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userGameInfos(): HasMany
    {
        return $this->hasMany(UserGameInfo::class);
    }
    
    public function teams()
    {
        return $this->belongsToMany(UserTeam::class, 'user_team_members')
                    ->withPivot('role')
                    ->withTimestamps();
    }
    public function ownedTeams()
    {
        return $this->hasMany(UserTeam::class, 'user_id');
    }

    // New relationship for sent invitations
    public function sentInvitations()
    {
        return $this->hasMany(Invitation::class, 'sender_id');
    }

}
