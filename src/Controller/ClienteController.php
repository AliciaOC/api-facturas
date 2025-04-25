<?php

namespace App\Controller;

use App\Entity\Cliente;
use App\Model\ClienteDatosCreacion;
use App\Repository\ClienteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * El decorador aquí es para no repetir el prefijo de la ruta en cada método. Igual con el nombre.
 * Si hay '' quiere decir que es la misma ruta, en este caso /api/clientes, y el controlador realiza una función u otra según el método HTTP.
 */
#[Route('/api/clientes', name: 'app_cliente_')]
final class ClienteController extends AbstractController
{
    private ClienteRepository $clienteRepository;
    private ValidatorInterface $validator;

    public function __construct(
        ClienteRepository $clienteRepository,
        ValidatorInterface $validator,
    ) {
        $this->clienteRepository = $clienteRepository;
        $this->validator = $validator;
    }

    /**
     * Solo va a funcionar con get y siempre va a devolver un json.
     */
    #[Route('', name: 'listar', methods: ['GET'], format: 'json')]
    public function listarClientes(): JsonResponse
    {
        return $this->json($this->clienteRepository->findAll());
    }

    /**
     * En la petición debe venir el id del cliente y ser un número entero.
     */
    #[Route('/{idCliente}', name: 'ver', methods: ['GET'], format: 'json', requirements: ['idCliente' => '\d+'])]
    public function verCliente(int $idCliente): JsonResponse|Response
    {
        $cliente = $this->clienteRepository->find($idCliente);

        if (empty($cliente)) {
            return new Response(null, Response::HTTP_NOT_FOUND);
        }

        return $this->json($cliente);
    }

    /**
     *MapRequestPayload es un decorador que se encarga de mapear el cuerpo de la petición a un objeto.
     *Comprueba que el objeto que se le pasa como argumento tenga las propiedades necesarias. En este caso nombre y direccion.
     *Manualmente se puede hacer con el método $request->getContent() y luego decodificarlo con json_decode(), obteniendo el array y asignando cada propiedad al objeto.
     */
    #[Route('', name: 'crear', methods: ['POST'], format: 'json')]
    public function crearCliente(
        #[MapRequestPayload()] ClienteDatosCreacion $datosCreacionCliente
    ): JsonResponse
    {
        $nuevoCliente = new Cliente();
        $nuevoCliente->setNombre($datosCreacionCliente->nombre);
        $nuevoCliente->setDireccion($datosCreacionCliente->direccion);

        $errores = $this->validator->validate($nuevoCliente);

        if (count($errores) > 0) {
            return $this->json($errores, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->clienteRepository->guardar($nuevoCliente);

        return $this->json($nuevoCliente);
    }
}
