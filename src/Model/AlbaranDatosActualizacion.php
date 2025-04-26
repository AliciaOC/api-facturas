<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class AlbaranDatosActualizacion
{
    /**
     * @param array<int,LineaAlbaranDatosActualizacion> $lineas
     */
    public function __construct(
        #[Assert\Type(type: 'integer')]
        #[Assert\Positive]
        public int $idCliente,

        /**
         * @var LineaAlbaranDatosActualizacion[] $lineas
         */
        #[Assert\All([new Assert\Type(LineaAlbaranDatosActualizacion::class)])]
        #[Assert\Optional] //No es obligatorio que haya lineas a actualizar
        public array $lineas,
    ) {
    }
}