<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Diario de Operaciones</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --bg:        #0d0f14;
            --surface:   #141720;
            --border:    #1e2330;
            --accent-r:  #e85d4a;
            --accent-y:  #e8a74a;
            --accent-g:  #4ae88a;
            --text-hi:   #f0f2f7;
            --text-mid:  #8b90a0;
            --text-lo:   #3d4255;
            --mono:      'DM Mono', monospace;
            --display:   'Syne', sans-serif;
            --body:      'DM Sans', sans-serif;
        }

        body {
            background-color: var(--bg);
            color: var(--text-hi);
            font-family: var(--body);
            min-height: 100vh;
            padding: 40px 20px 60px;
            -webkit-font-smoothing: antialiased;
        }

        .wrapper {
            max-width: 720px;
            margin: 0 auto;
        }

        /* ── HEADER ── */
        header {
            margin-bottom: 40px;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            animation: fadeDown 0.5s ease both;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .header-icon {
            width: 44px;
            height: 44px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .header-title {
            font-family: var(--display);
            font-size: 22px;
            font-weight: 800;
            color: var(--text-hi);
            letter-spacing: -0.3px;
            line-height: 1.2;
        }

        .header-subtitle {
            font-family: var(--mono);
            font-size: 12px;
            color: var(--text-mid);
            margin-top: 2px;
        }

        .badge-sistema {
            font-family: var(--mono);
            font-size: 11px;
            color: var(--accent-g);
            background: rgba(74, 232, 138, 0.08);
            border: 1px solid rgba(74, 232, 138, 0.2);
            padding: 5px 12px;
            border-radius: 20px;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .badge-dot {
            width: 6px;
            height: 6px;
            background: var(--accent-g);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        /* ── FECHA ── */
        .date-row {
            font-family: var(--mono);
            font-size: 12px;
            color: var(--text-mid);
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            gap: 8px;
            animation: fadeDown 0.5s 0.05s ease both;
        }

        .date-row::before {
            content: '';
            width: 20px;
            height: 1px;
            background: var(--text-lo);
        }

        /* ── STAT CARDS ── */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px 18px 18px;
            position: relative;
            overflow: hidden;
            transition: border-color 0.2s, transform 0.2s;
            animation: fadeUp 0.5s ease both;
        }

        .stat-card:nth-child(1) { animation-delay: 0.10s; --card-accent: var(--accent-r); }
        .stat-card:nth-child(2) { animation-delay: 0.17s; --card-accent: var(--accent-y); }
        .stat-card:nth-child(3) { animation-delay: 0.24s; --card-accent: var(--accent-g); }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: var(--card-accent);
            opacity: 0.8;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 120px;
            height: 120px;
            background: radial-gradient(circle, var(--card-accent) 0%, transparent 70%);
            opacity: 0.04;
            pointer-events: none;
        }

        .stat-card:hover {
            border-color: var(--card-accent);
            transform: translateY(-2px);
        }

        .stat-label {
            font-family: var(--mono);
            font-size: 10px;
            font-weight: 500;
            color: var(--text-mid);
            letter-spacing: 1.2px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .stat-value {
            font-family: var(--display);
            font-size: 48px;
            font-weight: 800;
            color: var(--card-accent);
            line-height: 1;
            margin-bottom: 6px;
            letter-spacing: -2px;
        }

        .stat-subtext {
            font-size: 12px;
            color: var(--text-mid);
        }

        /* ── SECTION CARDS ── */
        .section-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            margin-bottom: 16px;
            overflow: hidden;
            animation: fadeUp 0.5s 0.3s ease both;
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
        }

        .section-title {
            font-family: var(--display);
            font-size: 14px;
            font-weight: 700;
            color: var(--text-hi);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-count {
            font-family: var(--mono);
            font-size: 11px;
            color: var(--text-mid);
            background: var(--bg);
            border: 1px solid var(--border);
            padding: 3px 10px;
            border-radius: 20px;
        }

        /* ── CATEGORY LIST ── */
        .category-list {
            list-style: none;
        }

        .category-list li {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 13px 20px;
            border-bottom: 1px solid var(--border);
            transition: background-color 0.15s;
            gap: 12px;
        }

        .category-list li:last-child {
            border-bottom: none;
        }

        .category-list li:hover {
            background-color: rgba(255,255,255,0.02);
        }

        .category-name {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-hi);
            flex: 1;
        }

        /* Progress bar */
        .bar-wrap {
            flex: 2;
            height: 4px;
            background: var(--border);
            border-radius: 4px;
            overflow: hidden;
        }

        .bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #3d7fff, #63b3ff);
            border-radius: 4px;
            width: var(--pct, 50%);
            transition: width 0.6s ease;
        }

        .category-badge {
            font-family: var(--mono);
            font-size: 13px;
            font-weight: 500;
            color: var(--text-hi);
            background: var(--bg);
            border: 1px solid var(--border);
            padding: 3px 12px;
            border-radius: 6px;
            min-width: 44px;
            text-align: center;
        }

        /* ── SUMMARY BOX ── */
        .summary-box {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 18px 20px;
            margin-bottom: 16px;
            animation: fadeUp 0.5s 0.35s ease both;
        }

        .summary-label {
            font-family: var(--mono);
            font-size: 10px;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: var(--text-mid);
            margin-bottom: 10px;
        }

        .summary-text {
            font-size: 14px;
            color: var(--text-mid);
            line-height: 1.7;
        }

        .summary-text strong {
            color: var(--text-hi);
            font-weight: 600;
        }

        .summary-text .highlight-r { color: var(--accent-r); font-weight: 600; }
        .summary-text .highlight-g { color: var(--accent-g); font-weight: 600; }

        /* ── EMPTY STATE ── */
        .empty-state {
            padding: 32px 20px;
            text-align: center;
            color: var(--text-mid);
            font-size: 14px;
            font-style: italic;
        }

        /* ── FOOTER ── */
        footer {
            margin-top: 40px;
            padding: 18px 20px;
            border: 1px solid var(--border);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            animation: fadeUp 0.5s 0.4s ease both;
        }

        .footer-brand {
            font-family: var(--mono);
            font-size: 12px;
            color: var(--text-mid);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .footer-ts {
            font-family: var(--mono);
            font-size: 11px;
            color: var(--text-lo);
        }

        /* ── ANIMATIONS ── */
        @keyframes fadeDown {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.3; }
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 520px) {
            .stats-row {
                grid-template-columns: 1fr;
            }

            .stat-value {
                font-size: 40px;
            }

            header {
                flex-direction: column;
                gap: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">

        <!-- HEADER -->
        <header role="banner">
            <div class="header-left">
                <div class="header-icon" aria-hidden="true">📡</div>
                <div>
                    <div class="header-title">Reporte Diario de Operaciones</div>
                    <div class="header-subtitle">Sistema de Videovigilancia Municipal</div>
                </div>
            </div>
            <div class="badge-sistema" role="status" aria-label="Sistema activo">
                <span class="badge-dot" aria-hidden="true"></span>
                SISTEMA ACTIVO
            </div>
        </header>

        <!-- FECHA -->
        <div class="date-row" aria-label="Fecha del reporte">
            {{ $data['fecha'] }}
        </div>

        <!-- STAT CARDS -->
        <section aria-label="Estadísticas del día">
            <div class="stats-row">
                <div class="stat-card" role="region" aria-label="Total de intervenciones">
                    <div class="stat-label">Intervenciones</div>
                    <div class="stat-value" aria-live="polite">{{ $data['total_intervenciones'] }}</div>
                    <div class="stat-subtext">total del día</div>
                </div>

                <div class="stat-card" role="region" aria-label="Fallas registradas">
                    <div class="stat-label">Fallas</div>
                    <div class="stat-value" aria-live="polite">{{ $data['total_fallas'] }}</div>
                    <div class="stat-subtext">incidencias técnicas</div>
                </div>

                <div class="stat-card" role="region" aria-label="Expedientes recibidos">
                    <div class="stat-label">Expedientes</div>
                    <div class="stat-value" aria-live="polite">{{ $data['total_expedientes'] }}</div>
                    <div class="stat-subtext">nuevos ingresos</div>
                </div>
            </div>
        </section>

        <!-- INTERVENCIONES POR CATEGORÍA -->
        <div class="section-card" role="region" aria-labelledby="cat-title">
            <div class="section-header">
                <h2 class="section-title" id="cat-title">
                    <span aria-hidden="true">📋</span>
                    Intervenciones por categoría
                </h2>
                <span class="section-count">
                    {{ count($data['intervenciones_por_categoria']) }} categorías
                </span>
            </div>

            @if(count($data['intervenciones_por_categoria']) > 0)
                {{-- Calcula el máximo para las barras de progreso --}}
                @php
                    $maxTotal = $data['intervenciones_por_categoria']->max('total');
                @endphp
                <ul class="category-list" role="list">
                    @foreach($data['intervenciones_por_categoria'] as $item)
                    @php
                        $pct = $maxTotal > 0 ? round(($item->total / $maxTotal) * 100) : 0;
                    @endphp
                    <li role="listitem">
                        <span class="category-name">{{ $item->categoria->nombre }}</span>
                        <div class="bar-wrap" role="presentation">
                            <div class="bar-fill" style="--pct: {{ $pct }}%;" aria-hidden="true"></div>
                        </div>
                        <span class="category-badge" aria-label="{{ $item->total }} intervenciones">
                            {{ $item->total }}
                        </span>
                    </li>
                    @endforeach
                </ul>
            @else
                <div class="empty-state" role="status">
                    No hay intervenciones registradas en este período
                </div>
            @endif
        </div>

        <!-- RESUMEN DEL DÍA -->
        <div class="summary-box" role="complementary" aria-label="Resumen del día">
            <div class="summary-label">📌 Resumen del día</div>
            <p class="summary-text">
                Se realizaron <strong>{{ $data['total_intervenciones'] }}</strong> intervenciones en total,
                distribuidas en <strong>{{ count($data['intervenciones_por_categoria']) }}</strong> categorías diferentes.

                @if($data['total_fallas'] > 0)
                    Se registraron <span class="highlight-r">{{ $data['total_fallas'] }} fallas técnicas</span> que requirieron atención.
                @else
                    <span class="highlight-g">Sin fallas técnicas</span> durante el día.
                @endif

                @if($data['total_expedientes'] > 0)
                    Se recibieron <strong>{{ $data['total_expedientes'] }}</strong> nuevos expedientes.
                @endif
            </p>
        </div>

        <!-- FOOTER -->
        <footer role="contentinfo">
            <div class="footer-brand">
                <span aria-hidden="true">📱</span>
                Informe generado automáticamente — no responder
            </div>
            <div class="footer-ts">
                {{ date('d/m/Y H:i') }}
            </div>
        </footer>

    </div>
</body>
</html>