<?php

namespace App\Modules\Campaign\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    protected $fillable = [
        'name',
        'description',
        'status',
        'created_by',
        'max_players',
        'current_players',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Common\Models\User::class, 'created_by');
    }

    public function players(): HasMany
    {
        return $this->hasMany(CampaignPlayer::class);
    }
}
