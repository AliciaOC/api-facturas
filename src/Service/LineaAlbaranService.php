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
    public function crearLineaAlbaran(LineaAlbaranDatosCreacion $datosCreacionLineaAlbaran): LineaAlbaran
    {
        $nuevaLineaAlbaran = new LineaAlbaran();

        $nuevaLineaAlbaran->setProducto($datosCreacionLineaAlbaran->producto);
        $nuevaLineaAlbaran->setNombreProducto($datosCreacionLineaAlbaran->nombreProducto);
        $nuevaLineaAlbaran->setCantidad($datosCreacionLineaAlbaran->cantidad);
        $nuevaLineaAlbaran->setPrecioUnitario($datosCreacionLineaAlbaran->precioUnitario);

        $errores = $this->validator->validate($nuevaLineaAlbaran);

        if (count($errores) > 0) {
            throw new ErroresValidacionException($errores);
        }

        $this->lineaAlbaranRepository->guardar($nuevaLineaAlbaran);

        return $nuevaLineaAlbaran;
    }

    /**
     * @throws LineaAlbaranNoEncontradoException
     * @throws ErroresValidacionException
     */
    public function actualizarLineaAlbaran(int $idLineaAlbaran, LineaAlbaranDatosActualizacion $datosActualizacionLineaAlbaran): LineaAlbaran
    {
        /** @var LineaAlbaran|null $lineaAlbaran */
        $lineaAlbaran = $this->lineaAlbaranRepository->find($idLineaAlbaran);

        if ($lineaAlbaran->getProducto() !== $datosActualizacionLineaAlbaran->producto) {
            $lineaAlbaran->setProducto($datosActualizacionLineaAlbaran->producto);
        }

        if ($lineaAlbaran->getNombreProducto() !== $datosActualizacionLineaAlbaran->nombreProducto) {
            $lineaAlbaran->setNombreProducto($datosActualizacionLineaAlbaran->nombreProducto);
        }
        if ($lineaAlbaran->getCantidad() !== $datosActualizacionLineaAlbaran->cantidad) {
            $lineaAlbaran->setCantidad($datosActualizacionLineaAlbaran->cantidad);
        }
        if ($lineaAlbaran->getPrecioUnitario() !== $datosActualizacionLineaAlbaran->precioUnitario) {
            $lineaAlbaran->setPrecioUnitario($datosActualizacionLineaAlbaran->precioUnitario);
        }
        
        $errores = $this->validator->validate($lineaAlbaran);

        if (count($errores) > 0) {
            throw new ErroresValidacionException($errores);
        }

        $this->lineaAlbaranRepository->guardar($lineaAlbaran);

        return $lineaAlbaran;
    }

    /**
     * @throws LineaAlbaranNoEncontradoException
     */
    public function borrarLineaAlbaran(int $idLineaAlbaran): void
    {
        $lineaAlbaran = $this->lineaAlbaranRepository->find($idLineaAlbaran);

        $this->lineaAlbaranRepository->borrar($lineaAlbaran);
    }
}