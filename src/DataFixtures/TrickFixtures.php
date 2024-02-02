<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

class TrickFixtures extends Fixture implements DependentFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        /** @var Category $grab */
        $grab = $this->getReference('grab');
        /** @var Category $rotation */
        $rotation = $this->getReference('rotation');
        /** @var Category $flip */
        $flip = $this->getReference('flip');

        /** @var Image $image1 */
        $image1 = $this->getReference('image-1');
        /** @var Image $image2 */
        $image2 = $this->getReference('image-2');

        /** @var Video $video1 */
        $video1 = $this->getReference('video1');

        $trick1 = new Trick();
        $trick1->setName('Mute');
        $trick1->setCategory($grab);
        $trick1->addImage($image1);
        $trick1->addImage($image2);
        $trick1->setMainImage($image1);
        $manager->persist($trick1);

        $trick2 = new Trick();
        $trick2->setName('Sad');
        $trick2->setCategory($grab);
        $manager->persist($trick2);

        $trick3 = new Trick();
        $trick3->setName('Indy');
        $trick3->setCategory($grab);
        $manager->persist($trick3);

        $trick4 = new Trick();
        $trick4->setName('Stalefish');
        $trick4->setCategory($grab);
        $manager->persist($trick4);

        $trick5 = new Trick();
        $trick5->setName('Tail grab');
        $trick5->setCategory($grab);
        $trick5->addVideo($video1);
        $manager->persist($trick5);

        $trick6 = new Trick();
        $trick6->setName('Nose grab');
        $trick6->setCategory($grab);
        $manager->persist($trick6);

        $trick7 = new Trick();
        $trick7->setName('Japan');
        $trick7->setCategory($grab);
        $manager->persist($trick7);

        $trick8 = new Trick();
        $trick8->setName('Seat belt');
        $trick8->setCategory($grab);
        $manager->persist($trick8);

        $trick9 = new Trick();
        $trick9->setName('Truck driver');
        $trick9->setCategory($grab);
        $manager->persist($trick9);

        $trick10 = new Trick();
        $trick10->setName('180');
        $trick10->setCategory($rotation);
        $manager->persist($trick10);

        $trick11 = new Trick();
        $trick11->setName('360');
        $trick11->setCategory($rotation);
        $manager->persist($trick11);

        $trick12 = new Trick();
        $trick12->setName('540');
        $trick12->setCategory($rotation);
        $manager->persist($trick12);

        $trick13 = new Trick();
        $trick13->setName('720');
        $trick13->setCategory($rotation);
        $manager->persist($trick13);

        $trick14 = new Trick();
        $trick14->setName('900');
        $trick14->setCategory($rotation);
        $manager->persist($trick14);

        $trick15 = new Trick();
        $trick15->setName('1080');
        $trick15->setCategory($rotation);
        $manager->persist($trick15);

        $trick16 = new Trick();
        $trick16->setName('Front flip');
        $trick16->setCategory($flip);
        $manager->persist($trick16);

        $trick17 = new Trick();
        $trick17->setName('Back flip');
        $trick17->setCategory($flip);
        $manager->persist($trick17);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
            ImageFixtures::class,
        ];
    }
}
