## Trader App: Устройства трейдера, подключение APK и связь с реквизитами

### Назначение
Функционал предназначен для трейдеров: создание токенов устройств, подключение Android-приложения (APK) к аккаунту трейдера и привязка реквизитов к конкретному устройству. Через подключённые устройства в систему поступают СМС, по которым сервис автоматически мэчит и завершает заказы.

---

### Доменные сущности и хранилище
- **Модель**: `App\Models\UserDevice`
  - Таблица: `user_devices` (миграция `2025_03_03_151311_create_user_devices_table.php`).
  - Поля: `user_id`, `name`, `token` (уникальный), `android_id` (уникальный, nullable), `device_model`, `android_version`, `manufacturer`, `brand`, `connected_at`, timestamps.
  - Генерация токена: `UserDevice::generateToken()` (64-символьный random string).
  - Связь: `user()` → `belongsTo(User::class)`.

- **Связанные модели**:
  - `App\Models\PaymentDetail`: поле `user_device_id` (nullable) связывает реквизит с устройством.
  - `App\Models\SmsLog`: поле `user_device_id` логирует, с какого устройства пришла СМС.
  - `App\Models\Order`: при поиске подходящего заказа учитывается устройство, привязанное к реквизиту.

---

### Веб-интерфейс трейдера (UI)
- **Маршруты** (`routes/web.php`):
  - `GET /trader/devices` → `UserDeviceController@index`
  - `POST /trader/devices` → `UserDeviceController@store`
  - Скачивание APK: `GET /bridge.apk` → `ApkController@download` (имя маршрута: `app.download`).

- **Контроллер**: `App\Http\Controllers\UserDeviceController`
  - `index()`: отдает список устройств текущего пользователя, сортировка по `created_at desc`.
  - `store(Request)`: валидация `name`, создание `UserDevice` с `user_id`, `name`, `token`.

- **Страница**: `resources/js/Pages/UserDevice/Index.vue`
  - Форма создания токена (блокировка кнопки при `form.processing`).
  - Таблица устройств: имя, токен (копирование в буфер), статус подключения по `android_id`, создано/подключено (`DateTime` компонент).
  - Ссылка на загрузку APK: `route('app.download')`.

---

### API для приложения (APK)
- Базовый префикс: `/api/app` + middleware `device-access-token`.
  - Определено в `routes/api.php`:
    - `POST /api/app/device/connect` → `API\APP\DeviceController@connect`
    - `GET /api/app/device/ping` → `API\APP\DeviceController@ping`
    - `POST /api/app/sms` → `API\APP\SmsController@store` (c `idempotency_for_app`)
    - `GET /api/app/state` → `API\APP\StateController@index`

- **Аутентификация устройства**: middleware `App\Http\Middleware\DeviceAccessToken`
  - Читает заголовок `Access-Token`.
  - Ищет устройство через сервис `services()->device()->get($token)`; при отсутствии токена/устройства — 401.
  - Пробрасывает найденный `device` в `Request`.

- **Сервис устройств**: `App\Services\Device\DeviceService` (контракт `DeviceServiceContract`)
  - `get(string $token)`: кэширует поиск `UserDevice` по `token` на 10 минут.
  - `update(UserDevice $device, ...): UserDevice`: обновляет `android_id`, метаданные устройства и `connected_at`, инвалидирует кэш.

#### Подключение устройства: `POST /api/app/device/connect`
Вход: `android_id`, `device_model`, `android_version`, `manufacturer`, `brand` (в `body`); заголовок `Access-Token`.
Логика:
1. Валидация обязательных полей.
2. По `Access-Token` находим `UserDevice`.
3. Если `connected_at` уже установлен и `android_id` отличается — вернуть ошибку: токен занят другим устройством.
4. Если `connected_at` установлен и `android_id` совпадает — вернуть текущие данные (повторное подключение того же устройства).
5. Иначе обновить устройство (`update`) и вернуть `API\UserDeviceResource`.

Ответ: поля устройства без токена (`name`, `android_id`, `device_model`, `android_version`, `manufacturer`, `brand`, `connected_at`).

#### Пинг устройства: `GET /api/app/device/ping`
Логика:
1. По `Access-Token` находим устройство.
2. Если `android_id` пуст — 401 «Устройство не подключено».
3. Сохранить `cache()->put("user-apk-latest-ping-at-{user_id}", now())`.
4. Вернуть `success()`.

#### Состояние: `GET /api/app/state`
Логика:
1. Проверка подключения устройства (`android_id`).
2. Кэширование и возврат агрегатов по открытым диспутам трейдера: `latest_at`, `oldest_at`, `count` для `DisputeStatus::PENDING` по заказам трейдера, связанным с его реквизитами.
3. Обновление «последний пинг» в кеше.

#### Приём СМС: `POST /api/app/sms` (c идемпотентностью)
Вход: `sender`, `message`, `timestamp`, `type` + заголовок `Access-Token`.
Логика:
1. Проверка, что устройство подключено (`android_id`).
2. Обновить «последний пинг» в кеше.
3. Нормализовать отправителя и сообщение.
4. Пропустить, если отправитель в стоп-листе (`sender_stop_list`, кэш на 10 минут).
5. Поставить задачу `HandleSmsJob` (очередь `sms`) с `SmsDTO` (включая `deviceID`).

Обработка в `SmsService@handleSms`:
1. По `deviceID` получить `UserDevice` (кэш 10 минут) с `user`.
2. Записать `SmsLog` с `user_device_id` и `user_id`.
3. Распарсить СМС (Parser) для суммы/провайдера.
4. Найти PENDING заказ через `OrderQueriesEloquent::findPending(...)` с фильтрами:
   - точная сумма и валюта,
   - `trader_id = user.id`,
   - `payment_gateway_id` совпадает,
   - `paymentDetail.user_device_id = device.id`,
   - без `dispute`.
5. Если найден и `status = PENDING` — завершить: `order->finishOrderAsSuccessful(...)`, обновить `SmsLog.order_id`.

Идемпотентность (`IdempotencyForAppMiddleware`): в продакшене включает идемпотентность, ключ идемпотентности завязан на `device-{deviceID}` (иначе `global`).

---

### Связь устройств с реквизитами и формирование заказов
- В реквизитах (`PaymentDetail`) есть обязательная привязка к устройству: поле `user_device_id` (валидация и проверка принадлежности в `PaymentDetailController@store|update`).
- При выдаче реквизита для заказа (`FindAvailablePaymentDetail`):
  - Выбирается подходящий `PaymentDetail` по множеству критериев (лимиты, интервалы, уникальность сумм и т.п.).
  - Возвращаемое значение содержит `userDeviceID` из реквизита. Это устройство и будет использовано при мэчинге входящих СМС.
- При мэчинге СМС → заказов в `SmsService` используется именно `user_device_id` из реквизита: заказ считается найденным только если пришедшая СМС с того же устройства.

---

### Безопасность и ограничения
- Доступ к `/api/app/*` только с валидным заголовком `Access-Token` (существующий `user_devices.token`).
- Один токен → одно устройство: при попытке подключить другой `android_id` к уже подключенному токену будет ошибка.
- `android_id` хранится уникально в БД — одно физическое устройство нельзя привязать к нескольким токенам.
- Кэширование:
  - `device_by_token_{token}` — 10 минут (поиск устройства по токену).
  - `user-apk-latest-ping-at-{user_id}` — отметка «последний пинг».
  - `sender_stop_list` — кэш списка стоп-отправителей на 10 минут.

---

### Результат работы функционала
- Трейдер создаёт токен устройства в веб-панели, скачивает APK, вводит токен.
- APK делает `device/connect` и привязывает токен к физическому устройству (`android_id`).
- Трейдер привязывает свои реквизиты к выбранному устройству (`user_device_id`), через которое будет поступать СМС.
- При поступлении СМС:
  - СМС логируется с `user_device_id` и `user_id`.
  - Парсер извлекает сумму и провайдера.
  - Система находит PENDING заказ трейдера по тем же реквизитам (включая проверку устройства) и завершает его как успешный.

---

### Ключевые файлы
- Маршруты: `routes/web.php`, `routes/api.php`
- Модели: `App/Models/UserDevice.php`, `App/Models/PaymentDetail.php`, `App/Models/SmsLog.php`
- Контроллеры: `App/Http/Controllers/UserDeviceController.php`, `App/Http/Controllers/API/APP/DeviceController.php`, `SmsController.php`, `StateController.php`
- Ресурсы: `App/Http/Resources/UserDeviceResource.php`, `App/Http/Resources/API/UserDeviceResource.php`
- Middleware: `App/Http/Middleware/DeviceAccessToken.php`, `App/Http/Middleware/IdempotencyForAppMiddleware.php`
- Сервисы: `App/Services/Device/DeviceService.php`, `App/Services/Sms/SmsService.php`
- Очереди: `App/Jobs/HandleSmsJob.php`
- Поисковые запросы: `App/Queries/Eloquent/OrderQueriesEloquent.php`
- Выдача реквизитов: `App/Services/Order/Features/OrderDetailProvider/Classes/FindAvailablePaymentDetail.php`

---

### Примечания по UX и фронтенду
- Vue 3 + Inertia: в `UserDevice/Index.vue` соблюдены проектные правила (script над template, кнопки блокируются при запросе, используется готовый `MainTableSection`, `DateTime`).
- Для копирования токена используется `navigator.clipboard` с простым alert.
- После создания токена список устройств обновляется через слушатель `router.on('success', ...)`.


