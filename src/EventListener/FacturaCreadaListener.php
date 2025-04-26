<?php

namespace App\EventListener;

use App\Event\FacturaCreadaEvent;
use App\Model\AlbaranEstadosEnum;
use App\Repository\AlbaranRepository;
use App\Repository\FacturaRepository;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
class FacturaCreadaListener
{

    public function __construct( private AlbaranRepository $albaranRepository, private FacturaRepository $facturaRepository) 
    {
    }

    public function __invoke(FacturaCreadaEvent $event): void
    {
        $factura = $event->getFactura();
        foreach ($factura->getAlbaranes() as $albaran) {
            $albaran->setEstado(AlbaranEstadosEnum::Facturado);
            $albaran->setFactura($factura);
            
            $this->albaranRepository->guardar($albaran);
        }
        
    }
}