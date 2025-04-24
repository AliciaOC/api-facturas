<?php

namespace App\Model\Exceptions;

class DistintosClientesEnFacturaException extends \Exception
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