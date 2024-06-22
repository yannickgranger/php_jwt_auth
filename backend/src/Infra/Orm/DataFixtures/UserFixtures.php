<?php

namespace App\Infra\Orm\DataFixtures;

use App\Domain\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User(
            id: Uuid::fromString('31a2ee6f-0cf7-4202-924f-14b1a468cb59'),
            username: 'john.doe@example.com',
            email: 'john.doe@example.com',
            password: password_hash('jDoe@123_*ExAmPle.com', PASSWORD_DEFAULT)
        );
        $manager->persist($user);
        $manager->flush();
    }
}
