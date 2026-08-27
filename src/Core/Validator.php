<?php

declare(strict_types=1);

namespace App\Core;

use DateTime;

/**
 * Validación del lado del servidor. La del lado del cliente (HTML5 + JS)
 * es solo una mejora de experiencia; el servidor nunca confía en ella.
 *
 * Uso:
 *   $v = new Validator($_POST);
 *   $v->required('email', 'El email')->email('email');
 *   if ($v->fails()) { ... $v->errors() ... }
 */
class Validator
{
    private array $errores = [];

    public function __construct(private readonly array $datos)
    {
    }

    private function valor(string $campo): mixed
    {
        return $this->datos[$campo] ?? null;
    }

    private function agregarError(string $campo, string $mensaje): void
    {
        // Solo se guarda el primer error por campo, para no saturar el formulario.
        $this->errores[$campo] ??= $mensaje;
    }

    public function required(string $campo, string $etiqueta): static
    {
        $valor = $this->valor($campo);
        if ($valor === null || trim((string) $valor) === '') {
            $this->agregarError($campo, "$etiqueta es obligatorio.");
        }

        return $this;
    }

    public function email(string $campo, string $etiqueta = 'El email'): static
    {
        $valor = $this->valor($campo);
        if ($valor !== null && $valor !== '' && !filter_var($valor, FILTER_VALIDATE_EMAIL)) {
            $this->agregarError($campo, "$etiqueta no tiene un formato válido.");
        }

        return $this;
    }

    public function minLength(string $campo, int $min, string $etiqueta): static
    {
        $valor = (string) $this->valor($campo);
        if ($valor !== '' && mb_strlen($valor) < $min) {
            $this->agregarError($campo, "$etiqueta debe tener al menos $min caracteres.");
        }

        return $this;
    }

    public function maxLength(string $campo, int $max, string $etiqueta): static
    {
        $valor = (string) $this->valor($campo);
        if (mb_strlen($valor) > $max) {
            $this->agregarError($campo, "$etiqueta no puede superar los $max caracteres.");
        }

        return $this;
    }

    public function numeric(string $campo, string $etiqueta): static
    {
        $valor = $this->valor($campo);
        if ($valor !== null && $valor !== '' && !is_numeric($valor)) {
            $this->agregarError($campo, "$etiqueta debe ser un número.");
        }

        return $this;
    }

    public function matches(string $campo, string $otroCampo, string $etiqueta): static
    {
        if ($this->valor($campo) !== $this->valor($otroCampo)) {
            $this->agregarError($campo, "$etiqueta no coincide.");
        }

        return $this;
    }

    public function date(string $campo, string $etiqueta, string $formato = 'Y-m-d'): static
    {
        $valor = $this->valor($campo);
        if ($valor !== null && $valor !== '') {
            $fecha = DateTime::createFromFormat($formato, (string) $valor);
            if (!$fecha || $fecha->format($formato) !== $valor) {
                $this->agregarError($campo, "$etiqueta no es una fecha válida.");
            }
        }

        return $this;
    }

    public function fails(): bool
    {
        return count($this->errores) > 0;
    }

    public function errors(): array
    {
        return $this->errores;
    }

    public function error(string $campo): ?string
    {
        return $this->errores[$campo] ?? null;
    }
}
