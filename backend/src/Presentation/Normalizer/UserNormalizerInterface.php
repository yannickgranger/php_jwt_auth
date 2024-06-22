<?php

declare(strict_types=1);

namespace App\Presentation\Normalizer;

interface UserNormalizerInterface
{
    public function normalize(mixed $object, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null;
}
