<?php

namespace App\Controller;

use App\Event\FacturaCreadaEvent;
use App\Model\Exceptions\AlbaranNoEncontradoException;
use App\Model\Exceptions\AlbaranYaFacturadoException;
use App\Model\Exceptions\DistintosClientesEnFacturaException;
use App\Model\Exceptions\ErroresValidacionException;
use App\Model\FacturaDatosCreacion;
use App\Service\FacturaService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

final class FacturaController extends AbstractController
{
    private FacturaService $facturaService;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        FacturaService $facturaService,
        EventDispatcherInterface $eventDispatcher,
    ) {
        $this->facturaService = $facturaService;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[Route('/api/facturas', name: 'app_factura_crear', methods: ['POST'], format: 'json')]
    public function crearFactura(#[MapRequestPayload()] FacturaDatosCreacion $datosCreacionFactura): JsonResponse
    {
        try {
            $nuevaFactura = $this->facturaService->crearFactura($datosCreacionFactura);
            $evento = new FacturaCreadaEvent($nuevaFactura);
            $this->eventDispatcher->dispatch($evento);

            return $this->generarRespuestaJsonConReferenciasCirculares($nuevaFactura);
        } catch (AlbaranNoEncontradoException $e) {
            return $this->json(['error' => sprintf('No existe ningún albarán con ID %d.', $e->getIdAlbaran())], Response::HTTP_NOT_FOUND);
        } catch (AlbaranYaFacturadoException $e) {
            return $this->json(['error' => sprintf('El albarán con ID %d ya ha sido facturado.', $e->getIdAlbaran())], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (DistintosClientesEnFacturaException $e) {
            return $this->json(['error' => sprintf('El albarán con ID %d pertenece a un Cliente distinto a los anteriores albaranes.', $e->getIdAlbaran())], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (ErroresValidacionException $e) {
            return $this->generarRespuestaJsonConReferenciasCirculares($e->getErrores(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    private function generarRespuestaJsonConReferenciasCirculares($datos, int $codigoEstado = Response::HTTP_OK): JsonResponse
    {
        return $this->json($datos, $codigoEstado, [], [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function (object $object, ?string $format, array $context): ?int {
                return $object->getId();
            },
        ]);
    }
}
