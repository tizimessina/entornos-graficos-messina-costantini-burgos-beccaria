<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Cálculo de paginación (no ejecuta consultas: eso lo sigue haciendo el
 * modelo/controlador, usando $paginator->porPagina y $paginator->offset
 * en el LIMIT/OFFSET de la consulta).
 */
class Paginator
{
    public readonly int $totalRegistros;
    public readonly int $porPagina;
    public readonly int $totalPaginas;
    public readonly int $paginaActual;
    public readonly int $offset;

    public function __construct(int $totalRegistros, int $paginaActual = 1, int $porPagina = 10)
    {
        $this->totalRegistros = max(0, $totalRegistros);
        $this->porPagina      = max(1, $porPagina);
        $this->totalPaginas   = max(1, (int) ceil($this->totalRegistros / $this->porPagina));
        $this->paginaActual   = min(max(1, $paginaActual), $this->totalPaginas);
        $this->offset         = ($this->paginaActual - 1) * $this->porPagina;
    }

    public function hayAnterior(): bool
    {
        return $this->paginaActual > 1;
    }

    public function haySiguiente(): bool
    {
        return $this->paginaActual < $this->totalPaginas;
    }

    /** Números de página a mostrar, centrados en la actual (ej.: 3 4 [5] 6 7). */
    public function rango(int $delta = 2): array
    {
        $inicio = max(1, $this->paginaActual - $delta);
        $fin    = min($this->totalPaginas, $this->paginaActual + $delta);

        return range($inicio, $fin);
    }
}
