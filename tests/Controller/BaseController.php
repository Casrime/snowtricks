<?php

declare(strict_types=1);

namespace App\Tests\Controller;

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
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail($email);

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
