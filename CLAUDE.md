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
| `Intervencione` | `intervenciones` | Interventions/incidents; has pivot with `camaras` and `conocimientos` |
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

### Digifort HTTP API

Servers expose `http://{ip}:8601/Interface/...`. Credentials from `config('services.digifort.user/password')` or env `DIGIFORT_USER` / `DIGIFORT_PASSWORD`. Key endpoints:
- `GET /Interface/Server/GetStatus` — CPU, RAM, traffic
- `GET /Interface/Cameras/GetStatus` — per-camera status, FPS, disk usage, recording hours
- `GET /Interface/Server/GetLicenses` — license usage (used in `MonitoreoStats` widget)

### PDF Generation

`App\Models\Pdf` extends `Codedge\Fpdf\Fpdf\Fpdf` with a shared header (image from `public/img/headerReport.png`) and footer. PDFs are generated inline in Page action closures and returned via `response()->streamDownload()`. See `ListIntervenciones` for the full pattern.

### Permissions

- Managed by `FilamentShield` (Spatie Permission under the hood)
- Custom permissions declared in `config/filament-shield.php` under `custom_permissions`
- Widgets must use `HasWidgetShield` trait to be permission-gated
- Gate `viewApiDocs` defined in `AppServiceProvider` for Scramble docs access

### External Integrations

| Service | Config key | Notes |
|---|---|---|
| Digifort NVR | `services.digifort` | HTTP API on port 8601 |
| Telegram | `services.telegram.*` | 4 channels: datacenter, intervenciones, fallas, agua_presion |
| API Docs | `/docs/api` | Scramble (auto-generated from routes); excluded routes use `#[ExcludeRouteFromDocs]` |
| Activity Log | `activity_log` table | Spatie; shown as "Logs de usuarios" in panel |
| System Logs | `storage/logs/laravel.log` | Shown as "Logs de Sistema" via FilamentLogViewer |

### Dashboard Widgets

Located in `app/Filament/Widgets/`. Registered in `app/Filament/Pages/Dashboard.php`. `PresionAguaCharts` is instantiated dynamically per sensor topic. Widget sort order set via `protected static ?int $sort`.
