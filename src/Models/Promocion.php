<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Promocion
{
    /**
     * Devuelve la promoción solo si se puede aplicar a un vuelo de esa aerolínea:
     * aprobada por la administración, vigente hoy y con usos disponibles.
     *
     * Se llama dentro de la transacción de la reserva, con FOR UPDATE, para que
     * dos reservas simultáneas no puedan pasarse del tope de usos.
     */
    public static function buscarAplicable(string $codigo, int $codAerolinea): ?array
    {
        $stmt = Database::getInstance()->prepare(
            "SELECT codPromocion, codigoPromocion, descripcionPromocion, descuentoPromocion,
                    usosMaximos, usosRealizados
               FROM promociones
              WHERE codigoPromocion = :codigo
                AND codAerolinea = :aerolinea
                AND estadoPromocion = 'aprobada'
                AND CURDATE() BETWEEN fechaInicioPromocion AND fechaFinPromocion
                AND (usosMaximos IS NULL OR usosRealizados < usosMaximos)
              FOR UPDATE"
        );
        $stmt->execute([
            'codigo'    => $codigo,
            'aerolinea' => $codAerolinea,
        ]);

        return $stmt->fetch() ?: null;
    }

    public static function registrarUso(int $codPromocion): void
    {
        Database::getInstance()
            ->prepare('UPDATE promociones SET usosRealizados = usosRealizados + 1 WHERE codPromocion = :cod')
            ->execute(['cod' => $codPromocion]);
    }

    /** Al cancelar una reserva el uso se libera, así el código vuelve a estar disponible. */
    public static function liberarUso(int $codPromocion): void
    {
        Database::getInstance()
            ->prepare('UPDATE promociones SET usosRealizados = GREATEST(usosRealizados - 1, 0) WHERE codPromocion = :cod')
            ->execute(['cod' => $codPromocion]);
    }
}
