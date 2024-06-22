<?php

declare(strict_types=1);

namespace App\Tests\__Mock__;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

class InMemoryUserRepository implements UserRepositoryInterface, UserLoaderInterface
{
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->users->add(
            new User(
                id: Uuid::fromString('31a2ee6f-0cf7-4202-924f-14b1a468cb59'),
                username: 'john.doe@example.com',
                email: 'john.doe@example.com',
                password: 'jDoe@123_*ExAmPle.com'
            )
        );
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

    public function loadUserByIdentifier(string $identifier): ?UserInterface
    {
        foreach ($this->users->getIterator() as $user) {
            if($user->getIdentifier() === $identifier) {
                return $user;
            }
        }
    }
}
