<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class Usuario
{
    public static function find(int $id): ?array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT codusuario, nombreUsuario, emailUsuario, telefonoUsuario, tipoUsuario,
                    estadoCuenta, codAerolinea, fechaAltaUsuario
               FROM usuarios
              WHERE codusuario = :id'
        );
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public static function findPorEmail(string $email): ?array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT codusuario, nombreUsuario, claveUsuario, emailUsuario, tipoUsuario,
                    estadoCuenta, codAerolinea
               FROM usuarios
              WHERE emailUsuario = :email'
        );
        $stmt->execute(['email' => $email]);

        return $stmt->fetch() ?: null;
    }

    public static function existeEmail(string $email): bool
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT 1 FROM usuarios WHERE emailUsuario = :email'
        );
        $stmt->execute(['email' => $email]);

        return (bool) $stmt->fetchColumn();
    }

    /** Alta de un pasajero. Queda pendiente de validar el correo. */
    public static function crearPasajero(
        string $nombre,
        string $email,
        string $hashClave,
        ?string $telefono,
        string $tokenValidacion,
        string $tokenExpira
    ): int {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            "INSERT INTO usuarios
                (nombreUsuario, claveUsuario, tipoUsuario, emailUsuario, telefonoUsuario,
                 estadoCuenta, tokenValidacion, tokenValidacionExpira)
             VALUES
                (:nombre, :clave, 'usuario', :email, :telefono,
                 'pendiente_email', :token, :expira)"
        );
        $stmt->execute([
            'nombre'   => $nombre,
            'clave'    => $hashClave,
            'email'    => $email,
            'telefono' => $telefono,
            'token'    => $tokenValidacion,
            'expira'   => $tokenExpira,
        ]);

        return (int) $pdo->lastInsertId();
    }

    /**
     * Alta de un CEO de aerolínea. Primero valida el correo y después queda
     * esperando que la administración lo autorice, así que pasa por dos
     * filtros antes de poder entrar.
     */
    public static function crearCeo(
        string $nombre,
        string $email,
        string $hashClave,
        ?string $telefono,
        int $codAerolinea,
        string $tokenValidacion,
        string $tokenExpira
    ): int {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            "INSERT INTO usuarios
                (nombreUsuario, claveUsuario, tipoUsuario, emailUsuario, telefonoUsuario,
                 codAerolinea, estadoCuenta, tokenValidacion, tokenValidacionExpira)
             VALUES
                (:nombre, :clave, 'ceo de aerolinea', :email, :telefono,
                 :aerolinea, 'pendiente_email', :token, :expira)"
        );
        $stmt->execute([
            'nombre'    => $nombre,
            'clave'     => $hashClave,
            'email'     => $email,
            'telefono'  => $telefono,
            'aerolinea' => $codAerolinea,
            'token'     => $tokenValidacion,
            'expira'    => $tokenExpira,
        ]);

        return (int) $pdo->lastInsertId();
    }

    /** Devuelve el usuario solo si el token existe y todavía no venció. */
    public static function findPorTokenValidacion(string $token): ?array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT codusuario, nombreUsuario, emailUsuario, tipoUsuario, estadoCuenta
               FROM usuarios
              WHERE tokenValidacion = :token
                AND tokenValidacionExpira > NOW()'
        );
        $stmt->execute(['token' => $token]);

        return $stmt->fetch() ?: null;
    }

    /**
     * Quema el token de validación y define el estado siguiente: un pasajero
     * queda activo, pero un CEO todavía tiene que esperar a que la
     * administración autorice su cuenta.
     */
    public static function activar(int $id): void
    {
        $stmt = Database::getInstance()->prepare(
            "UPDATE usuarios
                SET estadoCuenta = CASE
                                     WHEN tipoUsuario = 'ceo de aerolinea' THEN 'pendiente_autorizacion'
                                     ELSE 'activo'
                                   END,
                    fechaValidacionEmail = NOW(),
                    tokenValidacion = NULL,
                    tokenValidacionExpira = NULL
              WHERE codusuario = :id"
        );
        $stmt->execute(['id' => $id]);
    }

    public static function guardarTokenRecuperacion(int $id, string $token, string $expira): void
    {
        $stmt = Database::getInstance()->prepare(
            'UPDATE usuarios
                SET tokenRecuperacion = :token,
                    tokenRecuperacionExpira = :expira
              WHERE codusuario = :id'
        );
        $stmt->execute([
            'id'     => $id,
            'token'  => $token,
            'expira' => $expira,
        ]);
    }

    public static function findPorTokenRecuperacion(string $token): ?array
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT codusuario, nombreUsuario, emailUsuario
               FROM usuarios
              WHERE tokenRecuperacion = :token
                AND tokenRecuperacionExpira > NOW()'
        );
        $stmt->execute(['token' => $token]);

        return $stmt->fetch() ?: null;
    }

    /** Cambia la contraseña y quema el token de recuperación, si había uno. */
    public static function actualizarClave(int $id, string $hashClave): void
    {
        $stmt = Database::getInstance()->prepare(
            'UPDATE usuarios
                SET claveUsuario = :clave,
                    tokenRecuperacion = NULL,
                    tokenRecuperacionExpira = NULL
              WHERE codusuario = :id'
        );
        $stmt->execute([
            'id'    => $id,
            'clave' => $hashClave,
        ]);
    }

    public static function actualizarPerfil(int $id, string $nombre, ?string $telefono): void
    {
        $stmt = Database::getInstance()->prepare(
            'UPDATE usuarios
                SET nombreUsuario = :nombre,
                    telefonoUsuario = :telefono
              WHERE codusuario = :id'
        );
        $stmt->execute([
            'id'       => $id,
            'nombre'   => $nombre,
            'telefono' => $telefono,
        ]);
    }

    // --- Gestión del administrador ----------------------------------------

    /** Listado filtrable por tipo y estado, para el panel de administración. */
    public static function todos(?string $tipo, ?string $estado, int $limite, int $offset): array
    {
        [$where, $params] = self::filtros($tipo, $estado);

        $stmt = Database::getInstance()->prepare(
            "SELECT u.codusuario, u.nombreUsuario, u.emailUsuario, u.tipoUsuario, u.estadoCuenta,
                    u.fechaAltaUsuario, a.nombreAerolinea
               FROM usuarios u
          LEFT JOIN aerolineas a ON a.codAerolinea = u.codAerolinea
              WHERE $where
              ORDER BY u.fechaAltaUsuario DESC
              LIMIT :limite OFFSET :offset"
        );

        foreach ($params as $clave => $valor) {
            $stmt->bindValue($clave, $valor);
        }
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function contarTodos(?string $tipo, ?string $estado): int
    {
        [$where, $params] = self::filtros($tipo, $estado);

        $stmt = Database::getInstance()->prepare("SELECT COUNT(*) FROM usuarios u WHERE $where");

        foreach ($params as $clave => $valor) {
            $stmt->bindValue($clave, $valor);
        }
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    /** @return array{0: string, 1: array<string, mixed>} */
    private static function filtros(?string $tipo, ?string $estado): array
    {
        $condiciones = ['1 = 1'];
        $params = [];

        if ($tipo !== null && $tipo !== '') {
            $condiciones[] = 'u.tipoUsuario = :tipo';
            $params[':tipo'] = $tipo;
        }

        if ($estado !== null && $estado !== '') {
            $condiciones[] = 'u.estadoCuenta = :estado';
            $params[':estado'] = $estado;
        }

        return [implode(' AND ', $condiciones), $params];
    }

    public static function contarPendientesDeAutorizacion(): int
    {
        return (int) Database::getInstance()
            ->query("SELECT COUNT(*) FROM usuarios WHERE estadoCuenta = 'pendiente_autorizacion'")
            ->fetchColumn();
    }

    /**
     * Cambia el estado de la cuenta. Se usa para autorizar un CEO, rechazarlo,
     * suspender a alguien o volver a habilitarlo.
     */
    public static function cambiarEstado(int $id, string $estado): bool
    {
        $stmt = Database::getInstance()->prepare(
            'UPDATE usuarios SET estadoCuenta = :estado WHERE codusuario = :id AND estadoCuenta <> :estado2'
        );
        $stmt->execute(['id' => $id, 'estado' => $estado, 'estado2' => $estado]);

        return $stmt->rowCount() > 0;
    }

    /** Hash guardado, para verificar la contraseña actual antes de cambiarla. */
    public static function hashDe(int $id): ?string
    {
        $stmt = Database::getInstance()->prepare(
            'SELECT claveUsuario FROM usuarios WHERE codusuario = :id'
        );
        $stmt->execute(['id' => $id]);
        $hash = $stmt->fetchColumn();

        return $hash === false ? null : (string) $hash;
    }
}
