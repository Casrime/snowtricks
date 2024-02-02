<?php

declare(strict_types=1);

namespace App\Tests\Controller;

class FrontControllerTest extends BaseController
{
    public function testHomePage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertSelectorTextContains('h1', 'SnowTricks');
    }

    public function testTricksPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tricks');

        $this->assertSelectorTextNotContains('h1', 'SnowTricks');
        $this->assertSelectorTextContains('h1', 'Tricks list');
    }
}
