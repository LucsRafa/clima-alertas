<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeatherLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'payload',
        'observed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'observed_at' => 'datetime',
    ];

    /**
     * City relation.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}

