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
        $rutaConfig = dirname(__DIR__, 2) . '/config/config.php';

        if (!file_exists($rutaConfig)) {
            throw new RuntimeException(
                'Falta config/config.php. Copiá config/config.example.php y completá tus datos locales.'
            );
        }

        $mailConfig = (require $rutaConfig)['mail'];

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
}
