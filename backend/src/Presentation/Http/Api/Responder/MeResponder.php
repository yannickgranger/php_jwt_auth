<?php

declare(strict_types=1);


namespace App\Presentation\Http\Api\Responder;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;

class MeResponder extends ApiResponder
{
    public function __construct(private readonly SerializerInterface $serializer)
    {}

    public function respond(UserInterface $user): JsonResponse
    {
        return new JsonResponse(
            $this->serializer->normalize($user, 'json')
        );
    }
}
