<?php

namespace App\Service;

use App\Entity\Albaran;
use App\Entity\Factura;
use App\Model\AlbaranEstadosEnum;
use App\Model\Exceptions\AlbaranNoEncontradoException;
use App\Model\Exceptions\AlbaranYaFacturadoException;
use App\Model\Exceptions\DistintosClientesEnFacturaException;
use App\Model\Exceptions\ErroresValidacionException;
use App\Model\FacturaDatosCreacion;
use App\Repository\AlbaranRepository;
use App\Repository\FacturaRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FacturaService
{
    private AlbaranRepository $albaranRepository;
    private FacturaRepository $facturaRepository;
    private ValidatorInterface $validator;

    public function __construct(
        AlbaranRepository $albaranRepository,
        FacturaRepository $facturaRepository,
        ValidatorInterface $validator,
    ) {
        $this->albaranRepository = $albaranRepository;
        $this->facturaRepository = $facturaRepository;
        $this->validator = $validator;
    }

    /**
     * @throws AlbaranNoEncontradoException
     * @throws AlbaranYaFacturadoException
     * @throws DistintosClientesEnFacturaException
     * @throws ErroresValidacionException
     */
    public function crearFactura(FacturaDatosCreacion $datosCreacionFactura): Factura
    {
        $importeTotal = 0.0;
        /** @var Cliente|null */
        $cliente = null;

        $nuevaFactura = new Factura();

        foreach ($datosCreacionFactura->albaranes as $idAlbaran) {
            /** @var Albaran|null $albaran */
            $albaran = $this->albaranRepository->find($idAlbaran);

            if (empty($albaran)) {
                throw new AlbaranNoEncontradoException($idAlbaran);
            }

            if ($albaran->getEstado() === AlbaranEstadosEnum::Facturado) {
                throw new AlbaranYaFacturadoException($idAlbaran);
            }

            if (!empty($cliente) && $albaran->getCliente()->getId() !== $cliente->getId()) {
                throw new DistintosClientesEnFacturaException($idAlbaran);
            } elseif (empty($cliente)) {
                $cliente = $albaran->getCliente();
            }

            foreach ($albaran->getLineas() as $linea) {
                $importeTotal += $linea->getCantidad() * $linea->getPrecioUnitario();
            }

            $nuevaFactura->addAlbaran($albaran);
        }
        
        $nuevaFactura->setImporteTotal($importeTotal);
        $nuevaFactura->setCliente($cliente);

        $errores = $this->validator->validate($nuevaFactura);

        if (count($errores) > 0) {
            throw new ErroresValidacionException($errores);
        }

        $this->facturaRepository->guardar($nuevaFactura);

        return $nuevaFactura;
    }
}