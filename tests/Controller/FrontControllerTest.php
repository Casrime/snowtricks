<?php

declare(strict_types=1);

namespace App\Tests\Controller;

class FrontControllerTest extends BaseController
{
    public function testHomePage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertSelectorTextContains('h1', 'Hello FrontController! ✅');
    }
}
