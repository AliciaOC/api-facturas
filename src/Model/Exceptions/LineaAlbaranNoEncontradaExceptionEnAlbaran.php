<?php

namespace App\Model\Exceptions;

class LineaAlbaranNoEncontradaExceptionEnAlbaran extends \Exception
{
    protected int $idLineaAlbaran;
    protected int $idAlbaran;

    public function __construct(int $idLineaAlbaran, int $idAlbaran)
    {
        $this->idLineaAlbaran = $idLineaAlbaran;
        $this->idAlbaran = $idAlbaran;

        parent::__construct();
    }

    public function getIdLineaAlbaran(): int
    {
        return $this->idLineaAlbaran;
    }

    public function getIdAlbaran(): int
    {
        return $this->idAlbaran;
    }
}