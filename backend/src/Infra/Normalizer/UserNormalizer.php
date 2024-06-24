<?php

declare(strict_types=1);

namespace App\Infra\Normalizer;

use App\Domain\Entity\User;
use App\Presentation\Normalizer\UserNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserNormalizer implements NormalizerInterface, UserNormalizerInterface
{
    public function normalize(mixed $object, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        return [
            'id' => $object->getId(),
            'username' => $object->getUsername(),
            'email' => $object->getEmail(),
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof User;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            User::class => true,
        ];
    }
}
