<?php

declare(strict_types=1);

namespace App\Presentation\Http\Api\Action;

use App\Presentation\Http\Api\Responder\MeResponder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsController]
#[Route(path: '/api/me', name: 'api_me', methods: ['GET'])]
class MeAction
{
    private MeResponder $meResponder;

    public function __construct(MeResponder $meResponder)
    {
        $this->meResponder = $meResponder;
    }

    public function __invoke(Request $request, UserInterface $user): JsonResponse
    {
        return $this->meResponder->respond($user);
    }
}
