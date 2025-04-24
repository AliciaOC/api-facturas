<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class FacturaDatosCreacion
{
    /**
     * @param array<int,int> $albaranes
     */
    public function __construct(
        #[Assert\All([
            new Assert\Type(type: 'integer'),
            new Assert\Positive(),
            new Assert\NotNull()
        ])]
        #[Assert\NotBlank]
        public array $albaranes,
    ) {
    }
}