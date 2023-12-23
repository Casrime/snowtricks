<?php

declare(strict_types=1);

namespace App\Tests\Controller;

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
}
