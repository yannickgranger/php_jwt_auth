<?php

declare(strict_types=1);

namespace App\Tests\__Mock__;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class InMemoryUserRepository implements UserRepositoryInterface
{
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function findAll(): array
    {
        return $this->users->toArray();
    }

    public function findByUsername(string $username): ?User
    {
        foreach ($this->users as $user) {
            if ($user->getUsername() === trim($username)) {
                return $user;
            }
        }

        return null;
    }

    public function existsByUsername(string $username): bool
    {
        foreach ($this->users as $user) {
            if ($user->getUsername() === trim($username)) {
                return true;
            }
        }

        return false;
    }

    public function existsByEmail(string $email): bool
    {
        foreach ($this->users as $user) {
            if ($user->getEmail() === trim($email)) {
                return true;
            }
        }

        return false;
    }

    public function save(User $user): void
    {
        $this->users->add($user);
    }
}
