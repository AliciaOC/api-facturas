<?php

namespace App\Model\Exceptions;

class AlbaranNoEncontradoException extends \Exception
{
    protected int $idAlbaran;

    public function __construct(int $idAlbaran)
    {
        $this->idAlbaran = $idAlbaran;

        parent::__construct();
    }

    public function getIdAlbaran(): int
    {
        return $this->idAlbaran;
    }
}