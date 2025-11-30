<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes;
    
    public $table = 'permissions';

    protected $fillable = ['name', 'slug', 'description'];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

     protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }
}