<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

#[Assert\Cascade]
class LineaAlbaranDatosModificacion
{
    /**
    * @param LineaAlbaranDatosActualizacion[] $actualizar
    * @param int[] $borrar
    * @param LineaAlbaranDatosCreacion[] $crear
    */
    public function __construct(
        #[Assert\All([new Assert\Type(LineaAlbaranDatosActualizacion::class)])]
        public array $actualizar = [],

        #[Assert\All([
            new Assert\Type('integer'),
            new Assert\Positive
        ])]
        public array $borrar = [],

        #[Assert\All([new Assert\Type(LineaAlbaranDatosCreacion::class)])]
        public array $crear = [],
    ) {
    }
}