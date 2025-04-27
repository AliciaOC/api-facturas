<?php

namespace App\Controller;

use App\Model\AlbaranDatosActualizacion;
use App\Model\AlbaranDatosCreacion;
use App\Model\Exceptions\AlbaranNoEncontradoException;
use App\Model\Exceptions\AlbaranYaFacturadoException;
use App\Model\Exceptions\ClienteNoEncontradoException;
use App\Model\Exceptions\ErroresValidacionException;
use App\Model\Exceptions\LineaAlbaranNoEncontradaExceptionEnAlbaran;
use App\Repository\AlbaranRepository;
use App\Service\AlbaranService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/api/albaranes', name: 'app_albaran_')]
final class AlbaranController extends AbstractController
{
    private AlbaranRepository $albaranRepository;
    private AlbaranService $albaranService;

    public function __construct(
        AlbaranRepository $albaranRepository,
        AlbaranService $albaranService,
    ) {
        $this->albaranRepository = $albaranRepository;
        $this->albaranService = $albaranService;
    }

    #[Route('', name: 'listar', methods: ['GET'], format: 'json')]
    public function listarAlbaranes(): JsonResponse
    {
        return $this->generarRespuestaJsonConReferenciasCirculares($this->albaranRepository->findAll());
    }

    #[Route('/{idAlbaran}', name: 'ver', methods: ['GET'], format: 'json', requirements: ['idAlbaran' => '\d+'])]
    public function verAlbaran(int $idAlbaran): JsonResponse|Response
    {
        $albaran = $this->albaranRepository->find($idAlbaran);

        if (empty($albaran)) {
            return new Response(null, Response::HTTP_NOT_FOUND);
        }

        return $this->generarRespuestaJsonConReferenciasCirculares($albaran);
    }

    #[Route('', name: 'crear', methods: ['POST'], format: 'json')]
    public function crearAlbaran(
        #[MapRequestPayload] AlbaranDatosCreacion $datosCreacionAlbaran
    ): JsonResponse
    {
        try {
            $nuevoAlbaran = $this->albaranService->crearAlbaran($datosCreacionAlbaran);
            
            return $this->generarRespuestaJsonConReferenciasCirculares($nuevoAlbaran);
        } catch (ClienteNoEncontradoException $e) {
            return $this->json(['error' => sprintf('No existe ningún cliente con ID %d.', $e->getIdCliente())], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (ErroresValidacionException $e) {
            return $this->json($e->getErrores(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[Route('/{idAlbaran}', name: 'actualizar', methods: ['PATCH'], format: 'json', requirements: ['idAlbaran' => '\d+'])]
    public function actualizarAlbaran(
        #[MapRequestPayload] AlbaranDatosActualizacion $datosActualizacionAlbaran,
        int $idAlbaran
    ): JsonResponse|Response
    {     
        try {
            $albaran = $this->albaranService->actualizarAlbaran($idAlbaran, $datosActualizacionAlbaran);

            return $this->generarRespuestaJsonConReferenciasCirculares($albaran);
        } catch (AlbaranNoEncontradoException $e) {
            return new Response(null, Response::HTTP_NOT_FOUND);
        } catch (AlbaranYaFacturadoException $e) {
            return $this->json(['error' => sprintf('El albarán ID %d no puede ser modificado ya que ya ha sido facturado.', $e->getIdAlbaran())], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (ClienteNoEncontradoException $e) {
            return $this->json(['error' => sprintf('No existe ningún cliente con ID %d.', $e->getIdCliente())], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (LineaAlbaranNoEncontradaExceptionEnAlbaran $e) {
            return $this->json(['error' => sprintf('La línea de albarán con ID %d no ha sido encontrada en el albarán ID %d.', $e->getIdLineaAlbaran(), $e->getIdAlbaran())], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (ErroresValidacionException $e) {
            return $this->json($e->getErrores(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[Route('/{idAlbaran}', name: 'borrar', methods: ['DELETE'], format: 'json', requirements: ['idAlbaran' => '\d+'])]
    public function borrarAlbaran(int $idAlbaran): Response
    {
        try {
            $this->albaranService->borrarAlbaran($idAlbaran);

            return new Response(); //Tras la eliminación no se devuelve nada.
        } catch (AlbaranNoEncontradoException $e) {
            return new Response(null, Response::HTTP_NOT_FOUND);            
        } catch (AlbaranYaFacturadoException $e) {
            return $this->json(['error' => sprintf('El albarán ID %d no puede ser eliminado ya que ya ha sido facturado.', $e->getIdAlbaran())], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Sin esto hay un bucle infinito cuando intento devolver directamente con json un objeto que tiene referencias circulares.
     * Por ejemplo: cada albaran contiene lineas y cada linea contiene un albaran, que contiene lineas y etc.
     * Con esto el bucle se rompe cuando vuelve a encontrar el mismo objeto.
     * El return de AbstractNormalizer tiene que devolver algo siempre, yo he elegido el id del objeto.
     */
    private function generarRespuestaJsonConReferenciasCirculares($datos, int $codigoEstado = Response::HTTP_OK): JsonResponse
    {
        return $this->json($datos, $codigoEstado, [], [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function (object $object, ?string $format, array $context): int {
                return $object->getId();
            },
        ]);
    }
}
