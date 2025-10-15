<?php

namespace App\Mail;

use App\Models\Alert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WeatherAlertMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Alert $alert, public array $weather)
    {
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject('Clima-Alertas: Alerta de Clima')
            ->markdown('mail.weather.alert', [
                'alert' => $this->alert,
                'weather' => $this->weather,
            ]);
    }
}

