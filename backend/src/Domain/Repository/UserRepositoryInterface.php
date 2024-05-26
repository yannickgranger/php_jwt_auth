<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\User;

interface UserRepositoryInterface
{
    public function findAll(): array;

    public function findByUsername(string $username): ?User;

    public function existsByUsername(string $username): bool;

    public function existsByEmail(string $email): bool;

    public function save(User $user): void;
}
