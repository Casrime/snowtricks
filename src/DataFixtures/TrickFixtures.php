<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Video;
use DateTimeImmutable;
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
        /** @var Image $image3 */
        $image3 = $this->getReference('image-3');
        /** @var Image $image4 */
        $image4 = $this->getReference('image-4');

        /** @var User $user1 */
        $user1 = $this->getReference('user1');
        /** @var User $user2 */
        $user2 = $this->getReference('user2');

        /** @var Video $video1 */
        $video1 = $this->getReference('video1');
        /** @var Video $video2 */
        $video2 = $this->getReference('video2');
        /** @var Video $video3 */
        $video3 = $this->getReference('video3');
        /** @var Video $video4 */
        $video4 = $this->getReference('video4');

        $trick1 = new Trick();
        $trick1->setName('Mute');
        $trick1->setCategory($grab);
        $trick1->addImage($image1);
        $trick1->addImage($image2);
        $trick1->addImage($image3);
        $trick1->addImage($image4);
        $trick1->setMainImage($image1);
        $trick1->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed interdum ac neque non mattis. Nulla id metus finibus, dignissim dui vel, pharetra sem. Nunc consequat, lorem a ultricies pharetra, orci lacus ultrices nisi, ut rutrum sem augue sed augue. Nam ex enim, feugiat in pulvinar id, porttitor a velit. Nam metus orci, venenatis in libero quis, vehicula porta lorem. Nunc non ipsum quis libero scelerisque posuere. Vivamus laoreet tortor nibh, et faucibus ligula lacinia id. Curabitur tincidunt neque ac vulputate sodales. Donec aliquet nisi libero, non aliquet dolor ullamcorper a. Etiam luctus id neque in fermentum. Nam ac orci ex. Fusce convallis purus odio, at lacinia erat bibendum sit amet. Aenean condimentum lacus eleifend lobortis vestibulum. In a urna et diam ullamcorper consectetur a ut leo. Curabitur mattis nisi ut ex tempus, a placerat turpis sodales. Donec vel metus metus.

Vestibulum sed dui at urna hendrerit finibus. Curabitur et lacus efficitur, ultrices purus id, auctor magna. Maecenas auctor, neque in eleifend volutpat, augue augue hendrerit urna, rhoncus sodales massa ligula sed libero. Nullam et justo justo. Donec tristique, mauris at interdum volutpat, lorem est condimentum erat, quis mollis velit augue pharetra tellus. Nunc lobortis commodo velit sit amet aliquet. Aenean nulla eros, lacinia at pretium quis, sodales at nisi. Integer vitae ornare odio. Nam tempor faucibus pretium. Maecenas sagittis a dui ut pharetra. Nam pellentesque neque ut varius rhoncus. Vivamus a egestas mauris. Nam finibus nunc dapibus magna efficitur venenatis. Phasellus est neque, dictum in feugiat at, molestie in mauris. Phasellus vehicula lacus nec aliquam accumsan.

Quisque placerat sollicitudin erat at lacinia. Proin placerat lectus lacus, sed maximus eros sagittis at. Nulla dictum nisi at bibendum blandit. Nullam ex metus, venenatis sed sagittis at, egestas eu est. Etiam tempor dictum ante, a iaculis dui vestibulum nec. Sed gravida justo quis ullamcorper egestas. Aliquam cursus nunc eu feugiat ullamcorper. Morbi venenatis consectetur enim. Mauris id facilisis ligula. Integer orci augue, facilisis sit amet gravida et, tincidunt vitae sem. Duis vel vehicula urna, in ultrices lorem. Mauris sed enim ac nisl lobortis posuere sed sollicitudin diam. Proin et arcu lacus. Proin est velit, rutrum non lacinia nec, aliquam ac ipsum. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.

Phasellus in dolor at arcu lacinia mollis. Nullam vel malesuada mi. Morbi at porttitor nibh. Morbi interdum, tortor non efficitur pretium, quam ex ultrices tellus, et bibendum elit enim sed arcu. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. In sagittis metus at tellus sagittis, nec blandit turpis ultricies. Sed egestas at lorem a aliquet. Nulla quis tincidunt lorem. In ultricies lacus quis diam scelerisque, ac lobortis est lacinia. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque sed augue sodales elit ornare rutrum.

Aliquam et justo velit. Mauris placerat ligula ex, eget tincidunt odio pharetra in. Morbi pharetra, odio vel auctor hendrerit, ex elit egestas neque, ut elementum purus enim eget augue. Pellentesque tempor iaculis dui, vitae varius augue. Duis commodo nisl dolor, ut condimentum ante rhoncus ut. Aliquam at maximus elit. Curabitur gravida ligula eu pellentesque vulputate. Phasellus vitae sem iaculis, vestibulum metus quis, auctor erat. Quisque sit amet ullamcorper odio, et luctus neque. Phasellus et fermentum mi. Fusce semper ante sodales erat vestibulum, vel vulputate lacus imperdiet. Sed pulvinar fringilla vehicula. Etiam et arcu quis justo sagittis commodo. Nulla suscipit consectetur accumsan.');
        $trick1->setUser($user1);
        $trick1->addVideo($video1);
        $trick1->addVideo($video2);
        $trick1->addVideo($video3);
        $trick1->addVideo($video4);
        $trick1->setUpdatedAt(new DateTimeImmutable());
        $this->addReference('trick1', $trick1);
        $manager->persist($trick1);

        $trick2 = new Trick();
        $trick2->setName('Sad');
        $trick2->setCategory($grab);
        $trick2->setUser($user1);
        $manager->persist($trick2);

        $trick3 = new Trick();
        $trick3->setName('Indy');
        $trick3->setCategory($grab);
        $trick3->setUser($user1);
        $manager->persist($trick3);

        $trick4 = new Trick();
        $trick4->setName('Stalefish');
        $trick4->setCategory($grab);
        $trick4->setUser($user1);
        $manager->persist($trick4);

        $trick5 = new Trick();
        $trick5->setName('Tail grab');
        $trick5->setCategory($grab);
        $trick5->addVideo($video1);
        $trick5->setUser($user1);
        $manager->persist($trick5);

        $trick6 = new Trick();
        $trick6->setName('Nose grab');
        $trick6->setCategory($grab);
        $trick6->setUser($user1);
        $manager->persist($trick6);

        $trick7 = new Trick();
        $trick7->setName('Japan');
        $trick7->setCategory($grab);
        $trick7->setUser($user1);
        $manager->persist($trick7);

        $trick8 = new Trick();
        $trick8->setName('Seat belt');
        $trick8->setCategory($grab);
        $trick8->setUser($user1);
        $manager->persist($trick8);

        $trick9 = new Trick();
        $trick9->setName('Truck driver');
        $trick9->setCategory($grab);
        $trick9->setUser($user1);
        $manager->persist($trick9);

        $trick10 = new Trick();
        $trick10->setName('180');
        $trick10->setCategory($rotation);
        $trick10->setUser($user1);
        $manager->persist($trick10);

        $trick11 = new Trick();
        $trick11->setName('360');
        $trick11->setCategory($rotation);
        $trick11->setUser($user1);
        $manager->persist($trick11);

        $trick12 = new Trick();
        $trick12->setName('540');
        $trick12->setCategory($rotation);
        $trick12->setUser($user1);
        $manager->persist($trick12);

        $trick13 = new Trick();
        $trick13->setName('720');
        $trick13->setCategory($rotation);
        $trick13->setUser($user1);
        $manager->persist($trick13);

        $trick14 = new Trick();
        $trick14->setName('900');
        $trick14->setCategory($rotation);
        $trick14->setUser($user1);
        $manager->persist($trick14);

        $trick15 = new Trick();
        $trick15->setName('1080');
        $trick15->setCategory($rotation);
        $trick15->setUser($user2);
        $manager->persist($trick15);

        $trick16 = new Trick();
        $trick16->setName('Front flip');
        $trick16->setCategory($flip);
        $trick16->setUser($user2);
        $manager->persist($trick16);

        $trick17 = new Trick();
        $trick17->setName('Back flip');
        $trick17->setCategory($flip);
        $trick17->setUser($user2);
        $manager->persist($trick17);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
            ImageFixtures::class,
            UserFixtures::class,
        ];
    }
}
