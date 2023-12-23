<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Override;

class CategoryFixtures extends Fixture
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        $category1 = new Category();
        $category1->setName('Grab');
        $this->addReference('grab', $category1);
        $manager->persist($category1);

        $category2 = new Category();
        $category2->setName('Rotation');
        $this->addReference('rotation', $category2);
        $manager->persist($category2);

        $category3 = new Category();
        $category3->setName('Flip');
        $this->addReference('flip', $category3);
        $manager->persist($category3);

        $manager->flush();
    }
}
