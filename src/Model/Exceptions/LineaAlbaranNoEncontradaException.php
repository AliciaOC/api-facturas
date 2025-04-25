<?php

namespace App\Model\Exceptions;

class LineaAlbaranNoEncontradaException extends \Exception
{
    protected int $idLineaAlbaran;

    public function __construct(int $idLineaAlbaran)
    {
        $this->idLineaAlbaran = $idLineaAlbaran;

        parent::__construct();
    }

    public function getIdLineaAlbaran(): int
    {
        return $this->idLineaAlbaran;
    }
}