## Установка и развертывание

Проект: p2p платежный сервис (Laravel + Inertia + Vue 3).

### Стек (актуально по `composer.json` / `package.json`)

- **Backend**: Laravel `^11.9`, PHP `^8.3`
- **Frontend**: Vue `^3.4` (Composition API), Inertia `^1.0`, Vite `^5`
- **UI**: TailwindCSS `^3.4`, Flowbite `^2.3`
- **Очереди**: по умолчанию `QUEUE_CONNECTION=database` (worker через Supervisor). Можно перейти на Redis.

Ключевые пакеты backend:
- `laravel/sanctum`, `spatie/laravel-permission`, `irazasyed/telegram-bot-sdk`, `square1/laravel-idempotency`

> Докер не используется.

### Требования

- **ОС/сервер**: Ubuntu 22–24 (или совместимый Linux)
- **PHP**: 8.3 + расширения `ext-bcmath`, `ext-gmp`, `ext-mbstring`
- **БД**: MySQL 8 (PostgreSQL не проверялся)
- **Node.js**: v22.2.0+
- **npm**: 10.7.0+

### Быстрая установка (с нуля)

#### 1) Подготовка окружения

- Установите зависимости PHP/Node, создайте БД и пользователя.
- Настройте веб-сервер так, чтобы **DocumentRoot указывал на** папку `public/`.

#### 2) Настройка `.env`

Скопируйте `.env.example` → `.env` и заполните минимум:

```dotenv
APP_NAME=
APP_URL=

DB_HOST=
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

QUEUE_CONNECTION=database
```

Дополнительные (кастомные) параметры:

```dotenv
TELEGRAM_BOT_NAME=
TELEGRAM_BOT_TOKEN=""
TELEGRAM_REDIRECT_URI=https://example.com/auth/telegram/callback
TELEGRAM_WEBHOOK_TOKEN=token
```

- `TELEGRAM_REDIRECT_URI`: редирект после авторизации в разделе уведомлений.
- `TELEGRAM_WEBHOOK_TOKEN`: секретный токен для валидации входящих webhook’ов.

#### 3) Установка зависимостей и сборка фронтенда

```bash
composer install --no-interaction --prefer-dist --optimize-autoloader

npm install
npm run build
```

#### 4) Ключ приложения и кэш

```bash
php artisan key:generate --force
php artisan optimize
```

#### 5) База данных: выберите сценарий

**Вариант A (рекомендуется для production/обновлений):** безопасные миграции без удаления данных:

```bash
php artisan migrate --force
```

**Вариант B (только для чистой/пустой БД, обычно для локальной разработки):** полная инициализация проекта командой:

```bash
php artisan app:install
```

Важно: `app:install` внутри делает `migrate:fresh` и **полностью удаляет** существующие таблицы/данные, затем создаёт первичные сущности (в т.ч. пользователя Admin).

### Очереди (Supervisor)

Очереди запускаются воркером:

```bash
php artisan queue:work --queue="default,callback,notification"
```

Рекомендуемый путь — настроить Supervisor по официальной документации Laravel: `https://laravel.com/docs/11.x/queues#supervisor-configuration`.

### Планировщик (Cron)

Добавьте в `crontab -e`:

```bash
* * * * * /usr/bin/php /path-to-project/artisan schedule:run >> /dev/null 2>&1
```
