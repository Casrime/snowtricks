<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Override;

class VideoFixtures extends Fixture
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        $video1 = new Video();
        $video1->setUrl('https://www.youtube.com/watch?v=2n6TyLtH2no');
        $this->addReference('video1', $video1);
        $manager->persist($video1);

        $manager->flush();
    }
}
