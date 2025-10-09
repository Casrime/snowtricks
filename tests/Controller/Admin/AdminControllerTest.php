<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Tests\Controller\BaseController;

final class AdminControllerTest extends BaseController
{
    public function testAdminPageWithoutLogin(): void
    {
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('GET', '/admin/');

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');
    }

    public function testAdminPageWithUserLogin(): void
    {
        $kernelBrowser = $this->loginUser();
        $kernelBrowser->request('GET', '/admin/');

        $this->assertResponseStatusCodeSame(403);
        $this->assertSelectorTextContains('h1.exception-message', 'Access Denied.');
    }

    public function testAdminPageWithAdminLogin(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('GET', '/admin/');

        $this->assertResponseStatusCodeSame(200);
        $this->assertPageTitleContains('Administration');
    }
}
