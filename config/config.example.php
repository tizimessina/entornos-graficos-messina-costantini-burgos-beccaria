<?php

// Copiá este archivo a config/config.php y completá tus datos locales.
// config/config.php NUNCA se sube al repositorio (está en .gitignore).

return [
    'db' => [
        'host'    => '127.0.0.1',
        'port'    => 3306,
        'name'    => 'aeroeb',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4',
    ],

    'mail' => [
        'host'       => 'smtp.gmail.com',
        'port'       => 587,
        'username'   => 'no-reply@aeroeb.com.ar',
        'password'   => '', // contraseña de aplicación, no la de la cuenta
        'encryption' => 'tls',
        'from_email' => 'no-reply@aeroeb.com.ar',
        'from_name'  => 'AeroEB',
        // A dónde llegan los avisos para la administración (mensajes nuevos del
        // formulario de contacto). Si se deja vacío, se usa from_email.
        'admin_email' => '',
    ],

    'app' => [
        'url'                   => 'http://localhost:8000',
        'env'                   => 'development', // development | production
        'timezone'              => 'America/Argentina/Buenos_Aires',
        'token_ttl_horas'       => 24,
        'horas_min_cancelacion' => 72,
        'horas_para_pagar'      => 24, // después de esto, la reserva pendiente se cancela sola
    ],
];
