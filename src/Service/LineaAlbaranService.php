<?php

namespace App\Service;

use App\Entity\LineaAlbaran;
use App\Model\LineaAlbaranDatosActualizacion;
use App\Model\LineaAlbaranDatosCreacion;
use App\Model\Exceptions\LineaAlbaranNoEncontradaException;
use App\Model\Exceptions\ErroresValidacionException;
use App\Repository\LineaAlbaranRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LineaAlbaranService
{
    private LineaAlbaranRepository $lineaAlbaranRepository;
    private ValidatorInterface $validator;

    public function __construct(
        LineaAlbaranRepository $lineaAlbaranRepository,
        ValidatorInterface $validator,
    ) {
        $this->lineaAlbaranRepository = $lineaAlbaranRepository;
        $this->validator = $validator;
    }

    /**
     * @throws ErroresValidacionException
     */
    public function generarLineaAlbaran(LineaAlbaranDatosCreacion $datosCreacionLineaAlbaran): LineaAlbaran
    {
        $nuevaLineaAlbaran = new LineaAlbaran();

        $nuevaLineaAlbaran->setProducto($datosCreacionLineaAlbaran->producto);
        $nuevaLineaAlbaran->setNombreProducto($datosCreacionLineaAlbaran->nombreProducto);
        $nuevaLineaAlbaran->setCantidad($datosCreacionLineaAlbaran->cantidad);
        $nuevaLineaAlbaran->setPrecioUnitario($datosCreacionLineaAlbaran->precioUnitario);

        return $nuevaLineaAlbaran;
    }
}