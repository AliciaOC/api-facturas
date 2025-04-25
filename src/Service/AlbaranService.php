<?php

namespace App\Service;

use App\Entity\Albaran;
use App\Entity\LineaAlbaran;
use App\Model\AlbaranDatosActualizacion;
use App\Model\AlbaranDatosCreacion;
use App\Model\AlbaranEstadosEnum;
use App\Model\Exceptions\AlbaranNoEncontradoException;
use App\Model\Exceptions\AlbaranYaFacturadoException;
use App\Model\Exceptions\ClienteNoEncontradoException;
use App\Model\Exceptions\ErroresValidacionException;
use App\Model\Exceptions\LineaAlbaranNoEncontradaException;
use App\Repository\AlbaranRepository;
use App\Repository\ClienteRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AlbaranService
{
    private AlbaranRepository $albaranRepository;
    private ClienteRepository $clienteRepository;
    private ValidatorInterface $validator;

    public function __construct(
        AlbaranRepository $albaranRepository,
        ClienteRepository $clienteRepository,
        ValidatorInterface $validator,
    ) {
        $this->albaranRepository = $albaranRepository;
        $this->clienteRepository = $clienteRepository;
        $this->validator = $validator;
    }

    /**
     * @throws ClienteNoEncontradoException
     * @throws ErroresValidacionException
     */
    public function crearAlbaran(AlbaranDatosCreacion $datosCreacionAlbaran): Albaran
    {
        $cliente = $this->clienteRepository->find($datosCreacionAlbaran->idCliente);

        if (empty($cliente)) {
            throw new ClienteNoEncontradoException($datosCreacionAlbaran->idCliente);
        }

        $nuevoAlbaran = new Albaran();
        $nuevoAlbaran->setCliente($cliente);

        foreach ($datosCreacionAlbaran->lineas as $linea) {
            $nuevaLineaAlbaran = new LineaAlbaran();
            $nuevaLineaAlbaran->setProducto($linea->producto);
            $nuevaLineaAlbaran->setNombreProducto($linea->nombreProducto);
            $nuevaLineaAlbaran->setCantidad($linea->cantidad);
            $nuevaLineaAlbaran->setPrecioUnitario($linea->precioUnitario);

            $nuevoAlbaran->addLinea($nuevaLineaAlbaran);
        }

        $errores = $this->validator->validate($nuevoAlbaran);

        if (count($errores) > 0) {
            throw new ErroresValidacionException($errores);
        }

        $this->albaranRepository->guardar($nuevoAlbaran);

        return $nuevoAlbaran;
    }

    /**
     * @throws AlbaranNoEncontradoException
     * @throws AlbaranYaFacturadoException
     * @throws ClienteNoEncontradoException
     * @throws ErroresValidacionException
     */
    public function actualizarAlbaran(int $idAlbaran, AlbaranDatosActualizacion $datosActualizacionAlbaran): Albaran
    {
        /** @var Albaran|null $albaran */
        $albaran = $this->albaranRepository->find($idAlbaran);

        if (empty($albaran)) {
            throw new AlbaranNoEncontradoException($idAlbaran);
        }

        if ($albaran->getEstado() === AlbaranEstadosEnum::Facturado) {
            throw new AlbaranYaFacturadoException($idAlbaran);
        }

        if ($albaran->getCliente()->getId() !== $datosActualizacionAlbaran->idCliente) {
            $cliente = $this->clienteRepository->find($datosActualizacionAlbaran->idCliente);

            if (empty($cliente)) {
                throw new ClienteNoEncontradoException($datosActualizacionAlbaran->idCliente);
            }

            $albaran->setCliente($cliente);
        }

        /**
         * Actualizacion de las lineas: 
         *  1. Si no hay líneas, no se hacen cambios
         *  2. Si hay líneas:
         *   2.1. Si es un array vacío borra todas las líneas
         *   2.2. Si hay contenido:
         *       2.2.1. Si hay líneas sin id, se añaden
         *       2.2.2. Si hay líneas con id pero el id no existe, se devuelve mensaje de error.
         *       2.2.3. Si hay líneas con id, se actualizan si hay cambios
         *       2.2.4. Se borran las líneas preexistentes que no estén en el array.
         */
        if(!empty($datosActualizacionAlbaran->lineas)) {
            //2.1. Borrar todas la líneas
            if (count($datosActualizacionAlbaran->lineas) === 0) {
                foreach ($albaran->getLineas() as $linea) {
                    $albaran->removeLinea($linea);
                }
            } else {
                $arrayIDsLineasPeticion=[];
                
                foreach ($datosActualizacionAlbaran->lineas as $linea) {
                    //2.2.1. Crea línea
                    if (empty($linea->id)) {
                        $nuevaLineaAlbaran = new LineaAlbaran();
                        $nuevaLineaAlbaran->setProducto($linea->producto);
                        $nuevaLineaAlbaran->setNombreProducto($linea->nombreProducto);
                        $nuevaLineaAlbaran->setCantidad($linea->cantidad);
                        $nuevaLineaAlbaran->setPrecioUnitario($linea->precioUnitario);

                        $albaran->addLinea($nuevaLineaAlbaran);
                    } else {
                        /** @var LineaAlbaran|null $lineaExistente */
                        $lineaExistente = $this->albaranRepository->findLineaById($linea->id);

                        //2.2.2. Mensaje de error
                        if (empty($lineaExistente)) {
                            throw new LineaAlbaranNoEncontradaException($idAlbaran);
                        }

                        //2.2.3. Actualiza línea
                        if ($lineaExistente !== null) {
                            if ($lineaExistente->getProducto() !== $linea->producto) {
                                $lineaExistente->setProducto($linea->producto);
                            }
                            if ($lineaExistente->getNombreProducto() !== $linea->nombreProducto) {
                                $lineaExistente->setNombreProducto($linea->nombreProducto);
                            }
                            if ($lineaExistente->getCantidad() !== $linea->cantidad) {
                                $lineaExistente->setCantidad($linea->cantidad);
                            }
                            if ($lineaExistente->getPrecioUnitario() !== $linea->precioUnitario) {
                                $lineaExistente->setPrecioUnitario($linea->precioUnitario);
                            }
                        }
                    }

                    $arrayIDsLineasPeticion[] = $linea->id;
                }
                //2.2.4. Borra líneas que no están en el array
                foreach ($albaran->getLineas() as $linea) {
                    if (!in_array($linea->getId(), $arrayIDsLineasPeticion)) {
                        $albaran->removeLinea($linea);
                    }
                }
            }
        }

        $errores = $this->validator->validate($albaran);

        if (count($errores) > 0) {
            throw new ErroresValidacionException($errores);
        }

        $this->albaranRepository->guardar($albaran);

        return $albaran;
    }

    /**
     * @throws AlbaranNoEncontradoException
     * @throws AlbaranYaFacturadoException
     */
    public function borrarAlbaran(int $idAlbaran): void
    {
        $albaran = $this->albaranRepository->find($idAlbaran);

        if (empty($albaran)) {
            throw new AlbaranNoEncontradoException($idAlbaran);
        }

        if ($albaran->getEstado() === AlbaranEstadosEnum::Facturado) {
            throw new AlbaranYaFacturadoException($idAlbaran);
        }

        $this->albaranRepository->borrar($albaran);
    }
}