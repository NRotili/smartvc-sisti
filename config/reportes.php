<?php

return [
    // Tipos de reportes disponibles para suscribir usuarios
    // (página "Suscripciones de Reportes" en el panel)
    'tipos' => [
        'diario' => 'Reporte diario de monitoreo',
        'mensual_operadores' => 'Reporte mensual de operadores',
    ],

    // Fallback: se usan solo si el reporte no tiene usuarios suscriptos
    'destinatarios' => [
        'jorgerberti@gmail.com',
        'pbaldini@villaconstitucion.gov.ar'
    ],

    // Reporte mensual de estadísticas de operadores
    'destinatarios_operadores' => [
        'jorgerberti@gmail.com',
        'pbaldini@villaconstitucion.gov.ar'
    ],
];