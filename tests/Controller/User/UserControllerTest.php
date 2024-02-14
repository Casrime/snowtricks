<?php

declare(strict_types=1);

namespace App\Tests\Controller\User;

use App\Tests\Controller\BaseController;

class UserControllerTest extends BaseController
{
    public function testUserPageWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/user/');

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');
    }

    public function testUserPageWithUserLogin(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/user/');

        $this->assertResponseStatusCodeSame(200);
    }

    public function testUserPageWithAdminLogin(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/');

        $this->assertResponseStatusCodeSame(200);
        $this->assertPageTitleContains('Administration');
    }
}
