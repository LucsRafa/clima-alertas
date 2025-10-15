<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'city_id',
        'temp_min',
        'temp_max',
        'rain',
        'notify_at',
        'dispatched_at',
        'channel',
        'telegram_chat_id',
        'active',
    ];

    protected $casts = [
        'temp_min' => 'float',
        'temp_max' => 'float',
        'rain' => 'boolean',
        'notify_at' => 'datetime',
        'dispatched_at' => 'datetime',
        'active' => 'boolean',
    ];

    /**
     * Owner user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * City related to this alert.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
