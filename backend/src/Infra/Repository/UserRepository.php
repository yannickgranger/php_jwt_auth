<?php

declare(strict_types=1);

namespace App\Infra\Repository;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findAll(): array
    {
        return $this->findAll();
    }

    public function existsByUsername(string $username): bool
    {
        return $this->findOneBy(['username' => $username]) instanceof User;
    }

    public function existsByEmail(string $email): bool
    {
        return $this->findOneBy(['email' => $email]) instanceof User;
    }

    public function save(User $user): void
    {
        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();
    }

    public function findByUsername(string $username): ?User
    {
        $qb = $this->createQueryBuilder('u');
        $qb->where('username = :username');
        $qb->setParameter('username', $username);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
