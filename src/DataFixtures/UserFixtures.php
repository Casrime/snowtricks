<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Override;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    #[Override]
    public function load(ObjectManager $manager): void
    {
        $user1 = new User();
        $user1->setUsername('admin');
        $user1->setEmail('admin@snowtricks.com');
        $user1->setPassword($this->passwordHasher->hashPassword($user1, 'pass123'));
        $user1->setRoles(['ROLE_ADMIN']);
        $user1->setActive(true);
        $this->addReference('user1', $user1);
        $manager->persist($user1);

        $user2 = new User();
        $user2->setUsername('user');
        $user2->setEmail('user@snowtricks.com');
        $user2->setPassword($this->passwordHasher->hashPassword($user2, 'pass123'));
        $user2->setRoles(['ROLE_USER']);
        $user2->setActive(true);
        $this->addReference('user2', $user2);
        $manager->persist($user2);

        $user3 = new User();
        $user3->setUsername('user-bis');
        $user3->setEmail('user-bis@snowtricks.com');
        $user3->setPassword($this->passwordHasher->hashPassword($user3, 'pass123'));
        $user3->setRoles(['ROLE_USER']);
        $this->addReference('user3', $user3);
        $manager->persist($user3);

        $user4 = new User();
        $user4->setUsername('user-ter');
        $user4->setEmail('user-ter@snowtricks.com');
        $user4->setPassword($this->passwordHasher->hashPassword($user4, 'pass123'));
        $user4->setRoles(['ROLE_USER']);
        $this->addReference('user4', $user4);
        $manager->persist($user4);

        $manager->flush();
    }
}
