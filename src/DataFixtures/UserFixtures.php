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
        $user1->setPassword($this->passwordHasher->hashPassword($user1, 'pass123'));
        $user1->setRoles(['ROLE_ADMIN']);
        $this->addReference('user1', $user1);
        $manager->persist($user1);

        $user2 = new User();
        $user2->setUsername('user');
        $user2->setPassword($this->passwordHasher->hashPassword($user2, 'pass123'));
        $user2->setRoles(['ROLE_USER']);
        $this->addReference('user2', $user2);
        $manager->persist($user2);

        $manager->flush();
    }
}
