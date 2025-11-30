<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use \DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRemark extends Model
{ 
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'created_user_id',
        'old_remark',
        'new_remark',
        'is_seen',
        'seen_user_id',
        'seen_at',
    ];

    protected $casts = [
        'is_seen' => 'boolean',
        'seen_at' => 'datetime',
    ];
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'seen_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    // The user this remark is about
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Manager who created the remark
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    // User who marked it as seen
    public function seener(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seen_user_id');
    }

    // Scope: Only unseen remarks
    public function scopeUnseen($query)
    {
        return $query->where('is_seen', false);
    }
}