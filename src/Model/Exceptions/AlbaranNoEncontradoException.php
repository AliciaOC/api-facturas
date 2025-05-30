<?php

namespace App\Model\Exceptions;

class AlbaranNoEncontradoException extends \Exception
{
    /**
     * guarda el id del albarán que no se ha encontrado
     */
    protected int $idAlbaran;

    public function __construct(int $idAlbaran)
    {
        $this->idAlbaran = $idAlbaran;

        parent::__construct();
    }

    /**
     * Devuelve el id del albarán que no se ha encontrado
     */
    public function getIdAlbaran(): int
    {
        return $this->idAlbaran;
    }
}