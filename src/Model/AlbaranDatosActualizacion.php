<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

#[Assert\Cascade]
class AlbaranDatosActualizacion
{
    public function __construct(
        #[Assert\Type(type: 'integer')]
        #[Assert\Positive]
        public ?int $idCliente,

        #[Assert\Type(type: LineaAlbaranDatosModificacion::class)]
        public ?LineaAlbaranDatosModificacion $lineas,
    ) {
    }
}