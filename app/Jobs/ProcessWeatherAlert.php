<?php

namespace App\Jobs;

use App\Mail\WeatherAlertMail;
use App\Models\Alert;
use App\Services\Weather\OpenWeatherService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class ProcessWeatherAlert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $alertId)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(OpenWeatherService $service): void
    {
        $alert = Alert::with(['user', 'city'])->find($this->alertId);
        if (! $alert || ! $alert->active) {
            return;
        }

        $weather = $service->currentByCoords((float) $alert->city->lat, (float) $alert->city->lon);

        if (! $this->matchesRules($alert, $weather)) {
            return; // No-op if conditions not met
        }

        if ($alert->channel === 'email') {
            Mail::to($alert->user->email)->queue(new WeatherAlertMail($alert, $weather));
        } elseif ($alert->channel === 'telegram' && $alert->telegram_chat_id) {
            $token = (string) config('services.telegram.bot_token');
            $text = sprintf(
                'Alerta de clima para %s: %s, %.1f°C',
                $alert->city->name,
                data_get($weather, 'weather.0.description', 'N/A'),
                (float) data_get($weather, 'main.temp', 0)
            );
            Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $alert->telegram_chat_id,
                'text' => $text,
            ]);
        }

        // Mark as processed (one-shot): avoid resending on future dispatches
        $alert->active = false;
        $alert->save();
    }

    /**
     * Verify if current weather matches alert rules.
     */
    protected function matchesRules(Alert $alert, array $weather): bool
    {
        $temp = (float) data_get($weather, 'main.temp', 0);
        $rainMm = (float) data_get($weather, 'rain.1h', 0);

        if (! is_null($alert->temp_min) && $temp < $alert->temp_min) {
            return false;
        }
        if (! is_null($alert->temp_max) && $temp > $alert->temp_max) {
            return false;
        }
        if (! is_null($alert->rain)) {
            $isRaining = $rainMm > 0;
            if ($alert->rain !== $isRaining) {
                return false;
            }
        }

        return true;
    }
}
