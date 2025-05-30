<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class AlbaranDatosCreacion
{
    /**
     * @param array<int,LineaAlbaranDatosCreacion> $lineas
     */
    public function __construct(
        #[Assert\Type(type: 'integer')]
        #[Assert\Positive]
        public int $idCliente,

        #[Assert\All([new Assert\Type(LineaAlbaranDatosCreacion::class)])]
        public array $lineas,
    ) {
    }
}