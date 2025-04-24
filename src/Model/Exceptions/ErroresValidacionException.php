<?php

namespace App\Model\Exceptions;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ErroresValidacionException extends \Exception
{
    protected ConstraintViolationListInterface $errores;

    public function __construct(ConstraintViolationListInterface $errores)
    {
        $this->errores = $errores;

        parent::__construct();
    }

    public function getErrores(): ConstraintViolationListInterface
    {
        return $this->errores;
    }
}