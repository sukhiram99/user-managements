<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use \DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable 
{
    use HasFactory, Notifiable, SoftDeletes;

    public $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'remark',
        'email_verified_at',
        'remember_token',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getIsManagerAttribute()
    {
        return $this->roles()->whereIn('id', [1])->exists();
    }

     public function getIsUserAttribute()
    {
        return $this->roles()->whereIn('id', [2])->exists();
    }


    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function hasRole($role): bool
    {
        if (is_string($role)) {
            return $this->roles->contains('slug', $role);
        }
        return $this->roles->contains($role);
    }

    public function hasPermission($permission): bool
    {
        foreach ($this->roles as $role) {
            if ($role->permissions->contains('slug', $permission)) {
                return true;
            }
        }
        return false;
    }

    public function givePermissionTo($permissionSlug)
    {
        // Helper if needed
    }

}