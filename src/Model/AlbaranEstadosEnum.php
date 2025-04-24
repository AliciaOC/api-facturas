<?php

namespace App\Model;

/**
 * Este no es un 'esquema' o 'modelo' de datos, es un 'diccionario' de los posibles estados del albarán.
 */
enum AlbaranEstadosEnum: string
{
    case Abierto = 'Abierto';
    case Facturado = 'Facturado';
}