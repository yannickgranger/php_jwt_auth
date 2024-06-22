<?php

declare(strict_types=1);

namespace App\Presentation\Http;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/ping', name: 'ping', methods: 'GET')]
class PingAction
{
    public function __invoke(Request $request): JsonResponse
    {
        return new JsonResponse('pong', Response::HTTP_OK);
    }
}
