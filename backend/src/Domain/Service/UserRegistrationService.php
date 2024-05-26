<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\User;
use App\Domain\Exception\EmailNotUniqueException;
use App\Domain\Exception\UsernameNotUniqueException;
use App\Domain\Repository\UserRepositoryInterface;
use App\Presentation\Dto\UserRegistrationDto;
use Symfony\Component\Uid\Uuid;

class UserRegistrationService
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(UserRegistrationDto $userDto): void
    {
        // Basic validations such as type or HTML5 validity are in Application Layer (after controller)
        // and before domain, here assertion of types are done by PHP8

        $id = Uuid::v4();
        $user = new User(
            id: $id,
            username: $userDto->getUsername(),
            email: $userDto->getEmail(),
            password: password_hash($userDto->getPassword(), PASSWORD_DEFAULT),
        );

        // Domain object validation
        if ($this->userRepository->existsByUsername($user->getUsername())) {
            throw new UsernameNotUniqueException('Username is already taken.');
        }
        if ($this->userRepository->existsByEmail($user->getEmail())) {
            throw new EmailNotUniqueException('Email is already taken.');
        }

        $this->userRepository->save($user);
    }
}
