<?php

namespace App\Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;

class LoginCode extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'expires_at',
        'used_at'
    ];

    public function user()
    {
        return $this->belongsTo(\App\Modules\Common\Models\User::class);
    }

    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now())
            ->whereNull('used_at');
    }
}
