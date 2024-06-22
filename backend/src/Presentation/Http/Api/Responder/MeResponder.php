<?php

declare(strict_types=1);

namespace App\Presentation\Http\Api\Responder;

use App\Presentation\Normalizer\UserNormalizerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;

class MeResponder extends ApiResponder
{
    public function __construct(private readonly UserNormalizerInterface $normalizer)
    {
    }

    public function respond(UserInterface $user): JsonResponse
    {
        return new JsonResponse(
            $this->normalizer->normalize($user, 'json')
        );
    }
}
