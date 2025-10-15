<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'country',
        'lat',
        'lon',
    ];

    /**
     * Users who favorited the city.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_cities')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    /**
     * Weather logs for this city.
     */
    public function weatherLogs(): HasMany
    {
        return $this->hasMany(WeatherLog::class);
    }

    /**
     * Alerts associated with this city.
     */
    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }
}

