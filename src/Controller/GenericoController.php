<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Este controlador es solo por si la petición del cliente solo incluye el '/' o el '/api'. En ambos casos muestra un mensaje.
 */
final class GenericoController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('app_api_home', [], Response::HTTP_MOVED_PERMANENTLY);
    }

    #[Route('/api', name: 'app_api_home')]
    public function api(): JsonResponse
    {
        return $this->json([
            'message' => '¡Bienvenido a mi API de prueba para Gametia!',
        ]);
    }
}
