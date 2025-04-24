<?php

namespace App\Model\Exceptions;

class ClienteNoEncontradoException extends \Exception
{
    protected int $idCliente;

    public function __construct(int $idCliente)
    {
        $this->idCliente = $idCliente;

        parent::__construct();
    }

    public function getIdCliente(): int
    {
        return $this->idCliente;
    }
}