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

        $video2 = new Video();
        $video2->setUrl('https://www.youtube.com/watch?v=VFecEFvpaaI');
        $this->addReference('video2', $video2);
        $manager->persist($video2);

        $manager->flush();
    }
}
