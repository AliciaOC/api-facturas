<?php

namespace App\Event;

use App\Entity\Factura;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Se lanza cada vez que se crea una factura.
 */
final class FacturaCreadaEvent extends Event
{
    private Factura $factura;

    public function __construct(Factura $factura)
    {
        $this->factura = $factura;
    }

    public function getFactura(): Factura
    {
        return $this->factura;
    }
}