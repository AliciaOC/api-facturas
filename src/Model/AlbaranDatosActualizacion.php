<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class AlbaranDatosActualizacion
{
    public function __construct(
        #[Assert\Type(type: 'integer')]
        #[Assert\Positive]
        public int $idCliente,

        #[Assert\All([new Assert\Type(LineaAlbaranDatosCreacion::class)])]
        #[Assert\Optional] //No es obligatorio que haya lineas a actualizar
        public array $lineas,
    ) {
    }
}