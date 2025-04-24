<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ClienteDatosCreacion
{
    public function __construct(
        #[Assert\NotBlank]
        public string $nombre,

        #[Assert\NotBlank]
        public string $direccion,
    ) {
    }
}