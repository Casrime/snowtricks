<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Tests\Controller\BaseController;

class AdminControllerTest extends BaseController
{
    public function testAdminPageWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/');

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');
    }

    public function testAdminPageWithUserLogin(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/admin/');

        $this->assertResponseStatusCodeSame(403);
        $this->assertSelectorTextContains('h1.exception-message', 'Access Denied.');
    }

    public function testAdminPageWithAdminLogin(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/admin/');

        $this->assertResponseStatusCodeSame(200);
        $this->assertPageTitleContains('Administration');
    }
}
