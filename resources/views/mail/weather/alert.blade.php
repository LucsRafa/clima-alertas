@component('mail::message')
# Alerta de Clima

Cidade: {{ $alert->city->name }}, {{ strtoupper($alert->city->country) }}

Canal: {{ $alert->channel }}
Notificar em: {{ $alert->notify_at->timezone(config('app.timezone'))->toDateTimeString() }}

Resumo atual:
- Temp: {{ data_get($weather, 'main.temp') }} °C
- Condição: {{ data_get($weather, 'weather.0.description') }}
- Chuva (1h): {{ data_get($weather, 'rain.1h', 0) }} mm

@component('mail::button', ['url' => config('app.url')])
Abrir App
@endcomponent

Obrigado,
{{ config('app.name') }}
@endcomponent

