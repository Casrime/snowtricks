<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Token;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

final class TokenFixtures extends Fixture implements DependentFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        /** @var User $user4 */
        $user4 = $this->getReference('user4');

        $token1 = new Token();
        $token1->setExpirationDate(new DateTimeImmutable('+1 year'));
        $token1->setUser($user4);
        $token1->setActive(true);

        $manager->persist($token1);

        $token2 = new Token();
        $token2->setExpirationDate(new DateTimeImmutable('-1 day'));
        $token2->setUser($user4);
        $token2->setActive(true);

        $manager->persist($token2);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
