# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Full dev environment (server + queue + logs + vite hot reload)
composer dev

# Run tests
composer test

# Single test
php artisan test --filter TestClassName

# Fresh setup
composer setup

# Code style (Laravel Pint)
./vendor/bin/pint

# After adding new Filament resources/widgets
php artisan filament:upgrade
```

## Architecture

**SmartVC** is a Laravel 12 + Filament 4 admin panel for the Villa Constitución city surveillance system. The single Filament panel lives at `/dashboard` (`DashboardPanelProvider`).

### Filament Resource Structure

Every resource follows a split-file convention — never put form, table, or infolist logic directly in the Resource class:

```
app/Filament/Resources/EntityName/
    EntityNameResource.php       ← navigation, model binding, getPages()
    Schemas/EntityNameForm.php   ← form fields via static configure(Schema $schema)
    Tables/EntityNameTable.php   ← table columns/filters via static configure(Table $table)
    Pages/
        ListEntityNames.php
        CreateEntityName.php
        EditEntityName.php
```

### Key Domain Models

| Model | Table | Notes |
|---|---|---|
| `Camara` | `camaras` | `status`, `grabando`, `mantenimiento`, `activa` flags; SoftDeletes |
| `DesperfectosCamara` | `desperfectos_camaras` | Fallas de cámaras; open when `hora_solucion` is null; SoftDeletes |
| `FallasCamara` | `fallas_camaras` | Tipo de falla (lookup table) |
| `Intervencione` | `intervenciones` | Interventions/incidents; has pivot with `camaras` y `conocimientos`; accessor `fuera_de_plazo` detecta carga tardía |
| `Expediente` | `expedientes` | Solicitudes de registros fílmicos |
| `Servidores` | `servidores` | Digifort NVR servers; `descripcion='Monitoreo'` identifies main ones |
| `SensoresPresione` | `sensores_presiones` | Water pressure sensors via MQTT/SNMP |

### Observers (via `#[ObservedBy]` attribute)

- `DesperfectosCamaraObserver` — on `created`/`updated`: sends Telegram to `canal_monitoreo_fallas`; errors are caught, logged with `Log::error()`, and notified to `super_admin` users
- `NotificacioneObserver` — on `created` of interventions: sends Telegram to `canal_monitoreo_intervenciones`

### Automatic Fault Detection (Webhook)

Digifort NVR sends HTTP POST events to two endpoints (no auth required):
- `POST /api/monitoreo/fallas/down` → creates `DesperfectosCamara`, sets `camara.status=0`
- `POST /api/monitoreo/fallas/up` → closes open fault, sets `camara.status=1`

Body is INI-format (`parse_ini_string`). Cameras in `mantenimiento=1` are ignored.

### Camera Status Sync (Scheduled)

`php artisan camaras:sincronizar-estado` runs every 30 minutes via the scheduler. It queries `GET /Interface/Cameras/GetStatus` on each Digifort server (returns all cameras at once) and reconciles discrepancies:
- Camera down in Digifort but `status=1` in DB → creates `DesperfectosCamara`, sets `status=0`
- Camera up in Digifort but `status=0` in DB → closes open fault, sets `status=1`
- Skips cameras with `mantenimiento=1`
- API returns `Working` as boolean (`true`/`false`), not integer

### Digifort HTTP API

Servers expose `http://{ip}:8601/Interface/...`. Credentials from `config('services.digifort.user/password')` or env `DIGIFORT_USER` / `DIGIFORT_PASSWORD`. Key endpoints:
- `GET /Interface/Server/GetStatus` — CPU, RAM, traffic
- `GET /Interface/Cameras/GetStatus` — per-camera status (all cameras at once if no `Cameras` param); fields: `Name`, `Working` (bool), `Active` (bool), `WrittingToDisk` (bool), `RecordingFPS`, `RecordingHours`
- `GET /Interface/Server/GetLicenses` — license usage (used in `MonitoreoStats` widget)

### PDF Generation

`App\Models\Pdf` extends `Codedge\Fpdf\Fpdf\Fpdf` with a shared header (image from `public/img/headerReport.png`) and footer. PDFs are generated inline in Page action closures and returned via `response()->streamDownload()`. See `ListIntervenciones` for the full pattern.

### Permissions

- Managed by `FilamentShield` (Spatie Permission under the hood)
- Custom permissions declared in `config/filament-shield.php` under `custom_permissions`
- Widgets must use `HasWidgetShield` trait to be permission-gated; widgets that only appear for specific roles can use `canView()` instead (e.g. `ResumenMensualWidget`)
- Gate `viewApiDocs` defined in `AppServiceProvider` for Scramble docs access

### External Integrations

| Service | Config key | Notes |
|---|---|---|
| Digifort NVR | `services.digifort` | HTTP API on port 8601 |
| Telegram | `services.telegram.*` | 4 channels: datacenter, intervenciones, fallas, agua_presion |
| API Docs | `/docs/api` | Scramble (auto-generated from routes); excluded routes use `#[ExcludeRouteFromDocs]` |
| Activity Log | `activity_log` table | Spatie; shown as "Logs de usuarios" in panel |
| System Logs | `storage/logs/` | Shown as "Logs de Sistema" via FilamentLogViewer (achyutn/filament-log-viewer); `LOG_MAX_SIZE_KB` env controla el límite de tamaño |

### Dashboard Widgets

Located in `app/Filament/Widgets/`. Registered in `app/Filament/Pages/Dashboard.php`. `PresionAguaCharts` is instantiated dynamically per sensor topic. Widget sort order set via `protected static ?int $sort`. All widgets use `protected static bool $isLazy = true` for deferred loading.

`ResumenMensualWidget` — visible only for roles `Operador de Monitoreo` and `Supervisor de Monitoreo`; shows personal monthly stats (interventions created, participated, team participation %). Supervisors also see active faults count.

### Filament 4 Gotchas

- **No usar `->viteTheme()`** en `DashboardPanelProvider` — rompe todos los estilos de Filament
- **`ApexChartWidget`**: `mount()` debe llamar a `parent::mount()` para que `$this->options` se inicialice y `$this->readyToLoad = true`. El método de actualización es `updateOptions()`, no `updateChartOptions()`
- **Widgets side-by-side en páginas custom**: usar `getFooterWidgets()` + `getFooterWidgetsColumns()`. Las clases Tailwind estándar (`col-span-*`) no funcionan en el grid de Filament
- **`TextColumn` en tablas**: `->getStateUsing()` funciona para estado dinámico; `->formatStateUsing()` no recibe `$record`; `->description()` no existe en Filament 4; `->recordClasses()` con clases Tailwind arbitrarias no funciona (CSS no compilado); `->color()` y `->tooltip()` sí funcionan. Para lógica compleja con `$record`, usar un accessor en el modelo y leerlo como columna normal
- **`HasWidgetShield`** en widgets que no necesitan permisos granulares: omitirlo y usar `canView()` directamente
- **Carbon `diffInHours()`**: devuelve valor negativo si la fecha argumento es anterior. Usar `$fechaInicio->diffInHours($fechaFin)` (de la más vieja a la más nueva) para obtener valor positivo

### Docker

El `Dockerfile` usa `php:8.3-apache`. El `docker-entrypoint.sh` corre `chown -R www-data:www-data storage/ bootstrap/cache` en cada arranque para evitar errores de permisos cuando archivos son creados como root durante deploys. Al hacer deploy en producción, correr dentro del contenedor:

```bash
git pull origin master
composer install --no-dev --optimize-autoloader
php artisan optimize:clear
```

### Logging

`LOG_CHANNEL=daily` con `LOG_DAILY_DAYS=30` (un archivo por día en `storage/logs/`). `LOG_MAX_SIZE_KB=10240` evita que FilamentLogViewer ignore archivos grandes.
