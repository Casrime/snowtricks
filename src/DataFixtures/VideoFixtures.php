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
        $video1->setUrl('https://www.youtube.com/embed/M5NTCfdObfs?si=83Ra1uOezVvf4xHX');
        $this->addReference('video1', $video1);
        $manager->persist($video1);

        $video2 = new Video();
        $video2->setUrl('https://www.youtube.com/embed/NnnsXEBwTHc?si=_a7MeXXGoPN4Mh4H');
        $this->addReference('video2', $video2);
        $manager->persist($video2);

        $video3 = new Video();
        $video3->setUrl('https://www.youtube.com/embed/wWgCIEpE0Ug?si=XJv2A4NEO5bhDU41');
        $this->addReference('video3', $video3);
        $manager->persist($video3);

        $video4 = new Video();
        $video4->setUrl('https://www.youtube.com/embed/k6aOWf0LDcQ?si=esAYWWAse1onnfTe');
        $this->addReference('video4', $video4);
        $manager->persist($video4);

        $manager->flush();
    }
}
