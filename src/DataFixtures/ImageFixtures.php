<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Override;

class ImageFixtures extends Fixture
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        $image1 = new Image();
        $image1->setName('snowtricks.jpg');
        $image1->setAlt('Image 1');
        $this->addReference('image-1', $image1);
        $manager->persist($image1);

        $image2 = new Image();
        $image2->setName('snowtricks-2.jpg');
        $image2->setAlt('Image 2');
        $this->addReference('image-2', $image2);
        $manager->persist($image2);

        $image3 = new Image();
        $image3->setName('snowtricks-3.jpg');
        $image3->setAlt('Image 3');
        $this->addReference('image-3', $image3);
        $manager->persist($image3);

        $manager->flush();
    }
}
