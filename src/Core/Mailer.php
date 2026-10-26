<?php

declare(strict_types=1);

namespace App\Core;

use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;
use RuntimeException;

/**
 * Envío de correo por SMTP con PHPMailer. Requiere config/config.php
 * con los datos de la cuenta de correo (ver config/config.example.php).
 */
class Mailer
{
    public static function enviar(string $paraEmail, string $paraNombre, string $asunto, string $cuerpoHtml): bool
    {
        $mailConfig = self::config();

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $mailConfig['host'];
            $mail->Port       = $mailConfig['port'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $mailConfig['username'];
            $mail->Password   = $mailConfig['password'];
            $mail->SMTPSecure = $mailConfig['encryption'] ?? PHPMailer::ENCRYPTION_STARTTLS;
            $mail->CharSet    = 'UTF-8';
            // Sin esto PHPMailer espera hasta 300 s: si el SMTP no responde,
            // el usuario se queda mirando la página cargar.
            $mail->Timeout    = 10;

            $mail->setFrom($mailConfig['from_email'], $mailConfig['from_name']);
            $mail->addAddress($paraEmail, $paraNombre);

            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body    = $cuerpoHtml;
            $mail->AltBody = strip_tags($cuerpoHtml);

            return $mail->send();
        } catch (PHPMailerException) {
            error_log('Error enviando correo a ' . $paraEmail . ': ' . $mail->ErrorInfo);

            return false;
        }
    }

    /**
     * Casilla de la administración, a la que llegan los avisos internos (por
     * ejemplo, un mensaje nuevo del formulario de contacto). Sale de
     * mail.admin_email; si no está, se usa la misma casilla que envía, que
     * seguro existe porque es la cuenta SMTP del sitio.
     */
    public static function correoAdministracion(): string
    {
        $mailConfig = self::config();

        return trim((string) ($mailConfig['admin_email'] ?? '')) ?: (string) $mailConfig['from_email'];
    }

    private static function config(): array
    {
        $rutaConfig = dirname(__DIR__, 2) . '/config/config.php';

        if (!file_exists($rutaConfig)) {
            throw new RuntimeException(
                'Falta config/config.php. Copiá config/config.example.php y completá tus datos locales.'
            );
        }

        return (require $rutaConfig)['mail'];
    }
}
