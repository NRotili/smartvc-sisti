<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Mensual de Operadores</title>
</head>
<body style="margin:0; padding:0; background-color:#f0f2f5; font-family:Arial,Helvetica,sans-serif; -webkit-text-size-adjust:100%;">

<!-- WRAPPER -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f0f2f5; padding:32px 16px;">
<tr><td align="center">

<!-- CONTAINER -->
<table role="presentation" width="640" cellpadding="0" cellspacing="0" border="0" style="max-width:640px; width:100%;">

    <!-- ── HEADER ── -->
    <tr>
        <td style="background-color:#1a2035; border-radius:12px 12px 0 0; padding:28px 32px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td style="vertical-align:middle;">
                        <p style="margin:0 0 4px 0; font-size:11px; font-weight:bold; letter-spacing:2px; text-transform:uppercase; color:#6b7a99;">
                            SISTI - Smart VC
                        </p>
                        <h1 style="margin:0; font-size:22px; font-weight:800; color:#ffffff; letter-spacing:-0.3px; line-height:1.2;">
                            Reporte Mensual de Operadores
                        </h1>
                    </td>
                    <td align="right" style="vertical-align:middle; padding-left:16px; white-space:nowrap;">
                        <span style="display:inline-block; background-color:#0d2433; color:#4ab8e8; font-size:11px; font-weight:bold; padding:6px 14px; border-radius:20px; letter-spacing:1px;">
                            &#128202; MENSUAL
                        </span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- ── MES ── -->
    <tr>
        <td style="background-color:#1e2540; padding:12px 32px; border-bottom:1px solid #252d45;">
            <p style="margin:0; font-size:12px; color:#6b7a99; font-family:Courier New,Courier,monospace;">
                📅 &nbsp;{{ $data['mes'] }}
            </p>
        </td>
    </tr>

    <!-- ── BODY ── -->
    <tr>
        <td style="background-color:#ffffff; padding:28px 32px;">

            <!-- STAT CARDS -->
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:28px;">
                <tr>
                    <!-- Card 1: Total intervenciones -->
                    <td width="31%" style="padding-right:8px; vertical-align:top;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="background-color:#f0f5ff; border-radius:10px; border-top:3px solid #3d7fff; padding:18px 14px; text-align:center;">
                                    <p style="margin:0 0 8px 0; font-size:10px; font-weight:bold; letter-spacing:1.5px; text-transform:uppercase; color:#60749b;">INTERVENCIONES</p>
                                    <p style="margin:0 0 4px 0; font-size:44px; font-weight:800; color:#3d7fff; line-height:1; letter-spacing:-2px;">{{ $data['total_equipo'] }}</p>
                                    <p style="margin:0; font-size:11px; color:#8090b0;">total del equipo</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <!-- Card 2: Operadores -->
                    <td width="31%" style="padding:0 4px; vertical-align:top;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="background-color:#edfaf3; border-radius:10px; border-top:3px solid #27ae60; padding:18px 14px; text-align:center;">
                                    <p style="margin:0 0 8px 0; font-size:10px; font-weight:bold; letter-spacing:1.5px; text-transform:uppercase; color:#3d8060;">OPERADORES</p>
                                    <p style="margin:0 0 4px 0; font-size:44px; font-weight:800; color:#27ae60; line-height:1; letter-spacing:-2px;">{{ count($data['operadores']) }}</p>
                                    <p style="margin:0; font-size:11px; color:#60a080;">en el equipo</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <!-- Card 3: Variación -->
                    <td width="31%" style="padding-left:8px; vertical-align:top;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                @php
                                    $variacion = $data['variacion'];
                                    $colorVar = $variacion === null ? '#6b7a99' : ($variacion >= 0 ? '#27ae60' : '#e85d4a');
                                    $bgVar = $variacion === null ? '#f4f5f8' : ($variacion >= 0 ? '#edfaf3' : '#fdf1f0');
                                @endphp
                                <td style="background-color:{{ $bgVar }}; border-radius:10px; border-top:3px solid {{ $colorVar }}; padding:18px 14px; text-align:center;">
                                    <p style="margin:0 0 8px 0; font-size:10px; font-weight:bold; letter-spacing:1.5px; text-transform:uppercase; color:#60749b;">VS MES ANTERIOR</p>
                                    <p style="margin:0 0 4px 0; font-size:44px; font-weight:800; color:{{ $colorVar }}; line-height:1; letter-spacing:-2px;">
                                        {{ $variacion === null ? '—' : ($variacion > 0 ? '+' : '') . $variacion . '%' }}
                                    </p>
                                    <p style="margin:0; font-size:11px; color:#8090b0;">variación</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- DIVIDER -->
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:24px;">
                <tr><td style="height:1px; background-color:#e8ecf2; font-size:0; line-height:0;">&nbsp;</td></tr>
            </table>

            <!-- SECCIÓN: Tabla de operadores -->
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:24px;">
                <tr>
                    <td style="padding-bottom:12px;">
                        <p style="margin:0; font-size:14px; font-weight:800; color:#1a2035;">
                            👮 &nbsp;Desempeño por operador
                        </p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                            <!-- Encabezado -->
                            <tr>
                                <td style="padding:8px 10px; font-size:10px; font-weight:bold; letter-spacing:1px; text-transform:uppercase; color:#6b7a99; border-bottom:2px solid #e8ecf2;">Operador</td>
                                <td align="center" style="padding:8px 6px; font-size:10px; font-weight:bold; letter-spacing:1px; text-transform:uppercase; color:#6b7a99; border-bottom:2px solid #e8ecf2;">Creó</td>
                                <td align="center" style="padding:8px 6px; font-size:10px; font-weight:bold; letter-spacing:1px; text-transform:uppercase; color:#6b7a99; border-bottom:2px solid #e8ecf2;">Presente</td>
                                <td align="center" style="padding:8px 6px; font-size:10px; font-weight:bold; letter-spacing:1px; text-transform:uppercase; color:#6b7a99; border-bottom:2px solid #e8ecf2;">Total</td>
                                <td align="center" style="padding:8px 6px; font-size:10px; font-weight:bold; letter-spacing:1px; text-transform:uppercase; color:#6b7a99; border-bottom:2px solid #e8ecf2;">% Equipo</td>
                                <td align="center" style="padding:8px 6px; font-size:10px; font-weight:bold; letter-spacing:1px; text-transform:uppercase; color:#6b7a99; border-bottom:2px solid #e8ecf2;">En plazo</td>
                            </tr>
                            @forelse($data['operadores'] as $index => $operador)
                            @php
                                $bgRow = ($index % 2 === 0) ? '#f8f9fc' : '#ffffff';
                                $colorPlazo = $operador['en_plazo'] === null ? '#9aa3b5' : ($operador['en_plazo'] >= 80 ? '#27ae60' : ($operador['en_plazo'] >= 50 ? '#e8a74a' : '#e85d4a'));
                            @endphp
                            <tr>
                                <td style="padding:11px 10px; font-size:13px; font-weight:600; color:#2c3a55; background-color:{{ $bgRow }};">{{ $operador['nombre'] }}</td>
                                <td align="center" style="padding:11px 6px; font-size:13px; color:#4a5568; background-color:{{ $bgRow }}; font-family:Courier New,Courier,monospace;">{{ $operador['creadas'] }}</td>
                                <td align="center" style="padding:11px 6px; font-size:13px; color:#4a5568; background-color:{{ $bgRow }}; font-family:Courier New,Courier,monospace;">{{ $operador['presentes'] }}</td>
                                <td align="center" style="padding:11px 6px; background-color:{{ $bgRow }};">
                                    <span style="display:inline-block; background-color:#1a2035; color:#ffffff; font-size:12px; font-weight:700; padding:3px 12px; border-radius:6px; font-family:Courier New,Courier,monospace;">{{ $operador['total'] }}</span>
                                </td>
                                <td align="center" style="padding:11px 6px; font-size:13px; color:#4a5568; background-color:{{ $bgRow }}; font-family:Courier New,Courier,monospace;">{{ $operador['participacion'] }}%</td>
                                <td align="center" style="padding:11px 6px; font-size:13px; font-weight:700; color:{{ $colorPlazo }}; background-color:{{ $bgRow }}; font-family:Courier New,Courier,monospace;">
                                    {{ $operador['en_plazo'] === null ? '—' : $operador['en_plazo'] . '%' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" style="padding:24px; text-align:center; font-size:13px; color:#9aa3b5; font-style:italic; background-color:#f8f9fc; border-radius:8px;">
                                    No hay operadores con el rol asignado
                                </td>
                            </tr>
                            @endforelse
                        </table>
                        <p style="margin:8px 0 0 0; font-size:11px; color:#9aa3b5;">
                            <strong>En plazo</strong>: % de intervenciones creadas dentro de las 2 horas del hecho.
                        </p>
                    </td>
                </tr>
            </table>

            <!-- DIVIDER -->
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:24px;">
                <tr><td style="height:1px; background-color:#e8ecf2; font-size:0; line-height:0;">&nbsp;</td></tr>
            </table>

            <!-- SECCIÓN: Top categorías -->
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td style="padding-bottom:12px;">
                        <p style="margin:0; font-size:14px; font-weight:800; color:#1a2035;">
                            📋 &nbsp;Categorías más frecuentes del mes
                        </p>
                    </td>
                </tr>
                @forelse($data['top_categorias'] as $index => $categoria)
                @php $bgRow = ($index % 2 === 0) ? '#f8f9fc' : '#ffffff'; @endphp
                <tr>
                    <td style="padding:0; background-color:{{ $bgRow }}; border-radius:6px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="padding:13px 14px; vertical-align:middle;">
                                    <p style="margin:0; font-size:13px; font-weight:600; color:#2c3a55;">{{ $categoria['nombre'] }}</p>
                                </td>
                                <td align="right" style="padding:13px 14px; vertical-align:middle; white-space:nowrap;">
                                    <span style="display:inline-block; background-color:#1a2035; color:#ffffff; font-size:13px; font-weight:700; padding:4px 14px; border-radius:6px; font-family:Courier New,Courier,monospace;">
                                        {{ $categoria['total'] }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr><td style="height:4px; font-size:0; line-height:0;">&nbsp;</td></tr>
                @empty
                <tr>
                    <td style="padding:24px; text-align:center; font-size:13px; color:#9aa3b5; font-style:italic; background-color:#f8f9fc; border-radius:8px;">
                        No hay intervenciones registradas en este período
                    </td>
                </tr>
                @endforelse
            </table>

        </td>
    </tr>

    <!-- ── FOOTER ── -->
    <tr>
        <td style="background-color:#1a2035; border-radius:0 0 12px 12px; padding:18px 32px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td style="vertical-align:middle;">
                        <p style="margin:0; font-size:12px; color:#6b7a99;">
                            📱 &nbsp;Informe generado automáticamente &mdash; no responder
                        </p>
                    </td>
                    <td align="right" style="vertical-align:middle;">
                        <p style="margin:0; font-size:11px; color:#3d4a66; font-family:Courier New,Courier,monospace;">
                            {{ date('d/m/Y H:i') }}
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

</table>
<!-- /CONTAINER -->

</td></tr>
</table>
<!-- /WRAPPER -->

</body>
</html>
