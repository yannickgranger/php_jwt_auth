<?php

declare(strict_types=1);

namespace App\Domain\Exception;

/**
 * thrown in app layer.
 */
class InvalidUserDataException extends UserRegistrationException
{
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(
            'Invalid data for registration'.PHP_EOL.
            $message, $code, $previous);
    }
}
