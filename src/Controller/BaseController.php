<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\Mail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

abstract class BaseController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Mail $mail,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    public function getMail(): Mail
    {
        return $this->mail;
    }

    public function getUserPasswordHasher(): UserPasswordHasherInterface
    {
        return $this->passwordHasher;
    }
}
