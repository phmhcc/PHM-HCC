<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
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

    public function roleRecord(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role', 'name');
    }

    public function hasPermissionTo(string $permission): bool
    {
        $permissions = $this->roleRecord?->permissions ?? collect();

        foreach ($permissions as $grantedPermission) {
            $grantedPermission = $grantedPermission->name;
            if (
                $grantedPermission === '*'
                || $grantedPermission === $permission
                || (str_ends_with($grantedPermission, '.*')
                    && str_starts_with($permission, substr($grantedPermission, 0, -1)))
            ) {
                return true;
            }
        }

        return false;
    }
}
