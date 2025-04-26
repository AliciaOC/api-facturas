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
use App\Model\Exceptions\LineaAlbaranNoEncontradaExceptionEnAlbaran;
use App\Model\LineaAlbaranDatosActualizacion;
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
         * Actualizaciones de las líneas. 
         */
        if(isset($datosActualizacionAlbaran->lineas)) {
            //Actualización
            if(!empty($datosActualizacionAlbaran->lineas->actualizar)){
                $encontrado = false;
                foreach ($datosActualizacionAlbaran->lineas->actualizar as $datosLinea) {
                    foreach($albaran->getLineas() as $lineaAlbaran){
                        if($lineaAlbaran->getId() === $datosLinea->id){
                            $this->actualizarLineaAlbaran($lineaAlbaran, $datosLinea);
                            $encontrado = true;
                            break 2;
                        }
                    }
                }
                if(!$encontrado){
                    throw new LineaAlbaranNoEncontradaExceptionEnAlbaran($datosLinea->id, $idAlbaran);
                }
            }

            //Borrado
            if(!empty($datosActualizacionAlbaran->lineas->borrar)){
                foreach ($datosActualizacionAlbaran->lineas->borrar as $idLinea) {
                    foreach($albaran->getLineas() as $lineaAlbaran){
                        if($lineaAlbaran->getId() === $idLinea){
                            $albaran->removeLinea($lineaAlbaran);
                            break;
                        }
                    }
                }
            }

            //Creación
            if(!empty($datosActualizacionAlbaran->lineas->crear)){
                foreach ($datosActualizacionAlbaran->lineas->crear as $datosLinea) {
                    $nuevaLineaAlbaran = new LineaAlbaran();
                    $nuevaLineaAlbaran->setProducto($datosLinea->producto);
                    $nuevaLineaAlbaran->setNombreProducto($datosLinea->nombreProducto);
                    $nuevaLineaAlbaran->setCantidad($datosLinea->cantidad);
                    $nuevaLineaAlbaran->setPrecioUnitario($datosLinea->precioUnitario);

                    $albaran->addLinea($nuevaLineaAlbaran);
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

    private function actualizarLineaAlbaran(LineaAlbaran $lineaAlbaran, LineaAlbaranDatosActualizacion $datos): LineaAlbaran
    {
        if (!empty($datos->producto)) {
            $lineaAlbaran->setProducto($datos->producto);
        }
        if (!empty($datos->nombreProducto)) {
            $lineaAlbaran->setNombreProducto($datos->nombreProducto);
        }
        if (!empty($datos->cantidad)) {
            $lineaAlbaran->setCantidad($datos->cantidad);
        }
        if (!empty($datos->precioUnitario)) {
            $lineaAlbaran->setPrecioUnitario($datos->precioUnitario);
        }
        
        return $lineaAlbaran;
    }
}