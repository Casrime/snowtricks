<?php

declare(strict_types=1);

namespace App\Tests\Controller\User;

use App\Tests\Controller\BaseController;

final class UserControllerTest extends BaseController
{
    public function testUserPageWithoutLogin(): void
    {
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('GET', '/user/');

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');
    }

    public function testUserPageWithUserLogin(): void
    {
        $kernelBrowser = $this->loginUser();
        $kernelBrowser->request('GET', '/user/');

        $this->assertResponseStatusCodeSame(200);
    }

    public function testUserPageWithAdminLogin(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('GET', '/user/');

        $this->assertResponseStatusCodeSame(200);
        $this->assertPageTitleContains($this->trans('app.administration'));
    }
}
