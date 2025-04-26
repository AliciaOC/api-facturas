<?php

namespace App\Event;

use App\Entity\Factura;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Se lanza cada vez que se crea una factura.
 */
final class FacturaCreadaEvent extends Event
{
    private Factura $facturaCreada;

    public function __construct(Factura $facturaCreada) {
        $this->facturaCreada=$facturaCreada;
    }

    public function getFacturaCreada(): Factura
    {
        return $this->facturaCreada;
    }
}