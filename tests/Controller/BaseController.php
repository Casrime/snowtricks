<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class BaseController extends WebTestCase
{
    public function getDoctrine(KernelBrowser $client): Registry
    {
        /**
         * @var Registry $doctrine
         */
        $doctrine = $client->getContainer()->get('doctrine');

        return $doctrine;
    }

    private function login(string $email): KernelBrowser
    {
        $client = static::createClient();
        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);

        /** @var User $testUser */
        $testUser = $userRepository->findOneBy([
            'email' => $email,
        ]);

        $client->loginUser($testUser);

        return $client;
    }

    public function loginUser(): KernelBrowser
    {
        return $this->login('user@snowtricks.com');
    }

    public function loginAdmin(): KernelBrowser
    {
        return $this->login('admin@snowtricks.com');
    }
}
