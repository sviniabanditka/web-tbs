<?php

namespace App\Modules\Campaign\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignPlayer extends Model
{
    protected $fillable = [
        'campaign_id',
        'user_id',
        'role',
        'joined_at',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Common\Models\User::class);
    }
}
