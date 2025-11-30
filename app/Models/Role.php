<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;

    public $table = 'roles';

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

    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }
}