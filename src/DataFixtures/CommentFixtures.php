<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        $comment1 = new Comment();
        /** @var Trick $trick1 */
        $trick1 = $this->getReference('trick1');
        /** @var User $user1 */
        $user1 = $this->getReference('user1');
        $comment1->setUser($user1);
        $comment1->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor.');
        $comment1->setTrick($trick1);
        $manager->persist($comment1);

        $comment2 = new Comment();
        /** @var User $user2 */
        $user2 = $this->getReference('user2');
        $comment2->setUser($user2);
        $comment2->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor.');
        $comment2->setTrick($trick1);
        $manager->persist($comment2);

        $comment3 = new Comment();
        /** @var User $user3 */
        $user3 = $this->getReference('user2');
        $comment3->setUser($user3);
        $comment3->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor.');
        $comment3->setTrick($trick1);
        $manager->persist($comment3);

        $comment4 = new Comment();
        /** @var User $user4 */
        $user4 = $this->getReference('user2');
        $comment4->setUser($user4);
        $comment4->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor.');
        $comment4->setTrick($trick1);
        $manager->persist($comment4);

        $comment5 = new Comment();
        $comment5->setUser($user1);
        $comment5->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor.');
        $comment5->setTrick($trick1);
        $manager->persist($comment5);

        $comment6 = new Comment();
        $comment6->setUser($user1);
        $comment6->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor.');
        $comment6->setTrick($trick1);
        $manager->persist($comment6);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            TrickFixtures::class,
            UserFixtures::class,
        ];
    }
}
