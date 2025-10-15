<<<<<<< HEAD
# Clima‑Alertas (API REST)

API de clima e alertas construída em Laravel, com autenticação via Sanctum, cache de resposta do provedor (OpenWeather) e disparo de alertas por e‑mail ou Telegram através de Jobs + Scheduler.

## Sumário

- O que é e principais features
- Arquitetura / Stack
- Subir com Docker (Sail)
- Configuração de ambiente (.env)
- Fluxos principais (Auth, Cidades, Clima, Alertas)
- Scheduler e Filas
- Postman (coleção e ambiente)
- Testes rápidos

## O que é e principais features

- Autenticação com Laravel Sanctum (register, login, me, logout)
- CRUD de cidades e favoritos por usuário
- Clima atual por cidade: cacheia em `weather_logs` por 1 hora
- Alertas configuráveis por usuário (temp_min, temp_max, chuva, canal email/telegram)
- Job `ProcessWeatherAlert` e comando `alerts:dispatch` agendado a cada minuto
- Envio de e‑mails (Mailpit para dev, SMTP real em produção) e Telegram via HTTP

## Arquitetura / Stack

- PHP 8.4 + Laravel 12 (Sail)
- Docker (Sail) no WSL2
- MySQL, Redis, Mailpit, Meilisearch (opcional)
- HTTP Client: `Illuminate\\Support\\Facades\\Http` (com `retry`)
- Filas: Redis + Jobs / `queue:work`
- Scheduler: `schedule:work` + comando `alerts:dispatch`

## Subir com Docker (Sail)

Pré‑requisitos: Docker Desktop + WSL2 ativos.

1. Subir containers:
   - `./vendor/bin/sail up -d`
2. Aguardar MySQL “healthy”:
   - `./vendor/bin/sail ps`
3. Migrar banco:
   - `./vendor/bin/sail artisan migrate`
4. (Opcional) Seed básico:
   - `./vendor/bin/sail artisan db:seed` (cria user de teste)

## Configuração de ambiente (.env)

Não versione `.env` (já está no `.gitignore`). Crie a partir do exemplo:

- `cp .env.example .env` (ou use o existente e ajuste as variáveis abaixo)

Variáveis importantes:

- App/HTTP:
  - `APP_URL=http://localhost:8080`
  - `APP_TIMEZONE=America/Sao_Paulo`
- Banco/Cache: já prontos para Sail (`DB_HOST=mysql`, `DB_PORT=3306`, `REDIS_HOST=redis`)
- OpenWeather:
  - `WEATHER_PROVIDER=openweather`
  - `OPENWEATHER_KEY=...` (crie em https://home.openweathermap.org/api_keys)
- E‑mail (dev): Mailpit
  - `MAIL_MAILER=smtp`, `MAIL_HOST=mailpit`, `MAIL_PORT=1025`
  - Painel: http://localhost:8025
- E‑mail (produção): SMTP real (ex.: Gmail via App Password)
  - `MAIL_HOST=smtp.gmail.com`, `MAIL_PORT=587`, `MAIL_ENCRYPTION=tls`
  - `MAIL_USERNAME=seu_email`, `MAIL_PASSWORD=APP_PASSWORD_GERADA`
  - `MAIL_FROM_ADDRESS=seu_email`

Após alterar `.env`, recarregue config:

- `./vendor/bin/sail artisan config:clear`

## Fluxos principais

Autenticação (Sanctum)

- `POST /api/auth/register` → retorna `token`
- `POST /api/auth/login` → retorna `token`
- `GET /api/auth/me` → requer `Authorization: Bearer <token>`
- `POST /api/auth/logout` → invalida o token atual

Cidades

- `GET /api/cities?q=<termo>`
- `POST /api/cities` → `{ name, country, lat, lon }`
- `GET /api/cities/{id}` / `DELETE /api/cities/{id}`
- Favoritos: `POST /api/cities/{id}/favorite`, `DELETE /api/cities/{id}/favorite`

Clima

- `GET /api/weather/{city}`
  - Usa cache em `weather_logs` por ~1 hora (`cached: true|false` no payload)

Alertas

- `POST /api/alerts` → `{ city_id, notify_at, channel, temp_min?, temp_max?, rain?, telegram_chat_id? }`
- `GET /api/alerts`, `DELETE /api/alerts/{id}`
  - O comando `alerts:dispatch` busca alertas com `notify_at <= now`, ainda não despachados (`dispatched_at is null`) e ativos, enfileira o Job e marca `dispatched_at`.
  - O Job `ProcessWeatherAlert` envia o e‑mail/telegram e desativa o alerta (`active=false`) para evitar reenvio.

## Scheduler e Filas

- Agendamento (a cada minuto):
  - `./vendor/bin/sail artisan schedule:work`
- Worker de filas (processa Jobs e Mails):
  - `./vendor/bin/sail artisan queue:work -v`

Dicas:

- Para testes imediatos sem fila: `QUEUE_CONNECTION=sync` e `alerts:dispatch` envia na hora.
- Verificar erros:
  - Logs: `./vendor/bin/sail pail`
  - Jobs falhos: `./vendor/bin/sail artisan queue:failed` / `queue:retry all`

## Postman

- Ambiente: `postman/Local.postman_environment.json` (`baseUrl` e `token`)
- Coleção: `postman/ClimaAlertas.postman_collection.json`
- Faça login e o teste da request salva `{{token}}` automaticamente; as demais usam Bearer e headers JSON por padrão.

## Testes rápidos (cURL)

Registrar:

```
curl -X POST http://localhost:8080/api/auth/register \
  -H "Content-Type: application/json" -d '{"name":"Alice","email":"alice@example.com","password":"secret123","password_confirmation":"secret123"}'
```

Login (copie `token` do JSON):

```
curl -X POST http://localhost:8080/api/auth/login \
  -H "Content-Type: application/json" -d '{"email":"alice@example.com","password":"secret123"}'
```

Criar cidade (ex.: Santos):

```
curl -X POST http://localhost:8080/api/cities \
  -H "Authorization: Bearer <TOKEN>" -H "Content-Type: application/json" \
  -d '{"name":"Santos","country":"BR","lat":-23.9608,"lon":-46.3336}'
```

Clima:

```
curl -H "Authorization: Bearer <TOKEN>" http://localhost:8080/api/weather/<CITY_ID>
```

Criar alerta (email):

```
curl -X POST http://localhost:8080/api/alerts \
  -H "Authorization: Bearer <TOKEN>" -H "Content-Type: application/json" \
  -d '{"city_id":<CITY_ID>,"notify_at":"2025-10-16 08:00:00","channel":"email"}'
```

Despachar + processar (manual):

```
./vendor/bin/sail artisan alerts:dispatch
./vendor/bin/sail artisan queue:work -v
```

## Segurança e versionamento

- O arquivo `.env` está no `.gitignore` (não suba credenciais ou chaves privadas).
- Use `.env.example` como base e documente variáveis necessárias.

---

Clima‑Alertas • Laravel + Sail • MySQL • Redis • Mailpit • OpenWeather
=======
# clima-alertas
>>>>>>> 217daa7ce9c2701e20089d8cd6e75c309ca0771e
