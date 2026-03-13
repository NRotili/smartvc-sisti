<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Diario de Operaciones</title>
</head>
<body style="margin:0; padding:0; background-color:#f0f2f5; font-family:Arial,Helvetica,sans-serif; -webkit-text-size-adjust:100%;">

<!-- WRAPPER -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f0f2f5; padding:32px 16px;">
<tr><td align="center">

<!-- CONTAINER -->
<table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px; width:100%;">

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
                            Reporte Diario de Monitoreo
                        </h1>
                    </td>
                    <td align="right" style="vertical-align:middle; padding-left:16px; white-space:nowrap;">
                        <span style="display:inline-block; background-color:#0d3326; color:#4ae88a; font-size:11px; font-weight:bold; padding:6px 14px; border-radius:20px; letter-spacing:1px;">
                            &#9679; ACTIVO
                        </span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- ── FECHA ── -->
    <tr>
        <td style="background-color:#1e2540; padding:12px 32px; border-bottom:1px solid #252d45;">
            <p style="margin:0; font-size:12px; color:#6b7a99; font-family:Courier New,Courier,monospace;">
                📅 &nbsp;{{ $data['fecha'] }}
            </p>
        </td>
    </tr>

    <!-- ── BODY ── -->
    <tr>
        <td style="background-color:#ffffff; padding:28px 32px;">

            <!-- STAT CARDS — tabla de 3 columnas -->
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:28px;">
                <tr>
                    <!-- Card 1: Intervenciones -->
                    <td width="31%" style="padding-right:8px; vertical-align:top;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="background-color:#fdf1f0; border-radius:10px; border-top:3px solid #e85d4a; padding:18px 14px; text-align:center;">
                                    <p style="margin:0 0 8px 0; font-size:10px; font-weight:bold; letter-spacing:1.5px; text-transform:uppercase; color:#9b6b66;">INTERVENCIONES</p>
                                    <p style="margin:0 0 4px 0; font-size:44px; font-weight:800; color:#e85d4a; line-height:1; letter-spacing:-2px;">{{ $data['total_intervenciones'] }}</p>
                                    <p style="margin:0; font-size:11px; color:#b08080;">total del día</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <!-- Card 2: Fallas -->
                    <td width="31%" style="padding:0 4px; vertical-align:top;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="background-color:#fdf6ec; border-radius:10px; border-top:3px solid #e8a74a; padding:18px 14px; text-align:center;">
                                    <p style="margin:0 0 8px 0; font-size:10px; font-weight:bold; letter-spacing:1.5px; text-transform:uppercase; color:#9b8060;">FALLAS</p>
                                    <p style="margin:0 0 4px 0; font-size:44px; font-weight:800; color:#e8a74a; line-height:1; letter-spacing:-2px;">{{ $data['total_fallas'] }}</p>
                                    <p style="margin:0; font-size:11px; color:#b09060;">incidencias</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <!-- Card 3: Expedientes -->
                    <td width="31%" style="padding-left:8px; vertical-align:top;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="background-color:#edfaf3; border-radius:10px; border-top:3px solid #27ae60; padding:18px 14px; text-align:center;">
                                    <p style="margin:0 0 8px 0; font-size:10px; font-weight:bold; letter-spacing:1.5px; text-transform:uppercase; color:#3d8060;">EXPEDIENTES</p>
                                    <p style="margin:0 0 4px 0; font-size:44px; font-weight:800; color:#27ae60; line-height:1; letter-spacing:-2px;">{{ $data['total_expedientes'] }}</p>
                                    <p style="margin:0; font-size:11px; color:#60a080;">nuevos ingresos</p>
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

            <!-- SECCIÓN: Intervenciones por categoría -->
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:24px;">
                <!-- Título sección -->
                <tr>
                    <td style="padding-bottom:12px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="vertical-align:middle;">
                                    <p style="margin:0; font-size:14px; font-weight:800; color:#1a2035;">
                                        📋 &nbsp;Intervenciones por categoría
                                    </p>
                                </td>
                                <td align="right" style="vertical-align:middle;">
                                    <span style="font-size:11px; color:#6b7a99; background-color:#f0f2f5; padding:3px 10px; border-radius:10px; font-family:Courier New,Courier,monospace;">
                                        {{ count($data['intervenciones_por_categoria']) }} categorías
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                @if(count($data['intervenciones_por_categoria']) > 0)
                    @foreach($data['intervenciones_por_categoria'] as $index => $item)
                    @php $bgRow = ($index % 2 === 0) ? '#f8f9fc' : '#ffffff'; @endphp
                    <tr>
                        <td style="padding:0; background-color:{{ $bgRow }}; border-radius:6px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="padding:13px 14px; vertical-align:middle;">
                                        <p style="margin:0; font-size:13px; font-weight:600; color:#2c3a55;">{{ $item->categoria->nombre }}</p>
                                    </td>
                                    <td align="right" style="padding:13px 14px; vertical-align:middle; white-space:nowrap;">
                                        <span style="display:inline-block; background-color:#1a2035; color:#ffffff; font-size:13px; font-weight:700; padding:4px 14px; border-radius:6px; font-family:Courier New,Courier,monospace;">
                                            {{ $item->total }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr><td style="height:4px; font-size:0; line-height:0;">&nbsp;</td></tr>
                    @endforeach
                @else
                    <tr>
                        <td style="padding:24px; text-align:center; font-size:13px; color:#9aa3b5; font-style:italic; background-color:#f8f9fc; border-radius:8px;">
                            No hay intervenciones registradas en este período
                        </td>
                    </tr>
                @endif
            </table>

            <!-- DIVIDER -->
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:24px;">
                <tr><td style="height:1px; background-color:#e8ecf2; font-size:0; line-height:0;">&nbsp;</td></tr>
            </table>

            <!-- RESUMEN DEL DÍA -->
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td style="background-color:#f5f7ff; border-left:4px solid #3d7fff; border-radius:0 8px 8px 0; padding:16px 18px;">
                        <p style="margin:0 0 6px 0; font-size:10px; font-weight:bold; letter-spacing:1.5px; text-transform:uppercase; color:#6b7a99;">
                            📌 &nbsp;Resumen del día
                        </p>
                        <p style="margin:0; font-size:13px; color:#4a5568; line-height:1.7;">
                            Se realizaron <strong style="color:#1a2035;">{{ $data['total_intervenciones'] }}</strong> intervenciones,
                            distribuidas en <strong style="color:#1a2035;">{{ count($data['intervenciones_por_categoria']) }}</strong> categorías.

                            @if($data['total_fallas'] > 0)
                                Se registraron <strong style="color:#e85d4a;">{{ $data['total_fallas'] }} fallas técnicas</strong> que requirieron atención.
                            @else
                                <strong style="color:#27ae60;">Sin fallas técnicas</strong> durante el día.
                            @endif

                            @if($data['total_expedientes'] > 0)
                                Se recibieron <strong style="color:#1a2035;">{{ $data['total_expedientes'] }}</strong> nuevos expedientes.
                            @endif
                        </p>
                    </td>
                </tr>
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