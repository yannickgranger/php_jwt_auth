<?php

declare(strict_types=1);

namespace App\Presentation\Http\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/time', name: 'time', methods: 'GET')]
class TimeAction
{
    public function __invoke(Request $request): JsonResponse
    {
        $time = new \DateTimeImmutable('now');

        return new JsonResponse($time, Response::HTTP_OK);
    }
}
