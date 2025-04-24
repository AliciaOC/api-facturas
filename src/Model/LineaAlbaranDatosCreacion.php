<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class LineaAlbaranDatosCreacion
{
    public function __construct(
        #[Assert\Type(type: 'integer')]
        #[Assert\Positive]
        public int $producto,
    
        #[Assert\NotBlank]
        public string $nombreProducto,
    
        #[Assert\Type(type: 'float')]
        #[Assert\Positive]
        public float $cantidad,
    
        #[Assert\Type(type: 'float')]
        #[Assert\Positive]
        public float $precioUnitario,
    ) {
    }
}