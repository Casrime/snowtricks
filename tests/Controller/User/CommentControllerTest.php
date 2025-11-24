<?php

declare(strict_types=1);

namespace App\Tests\Controller\User;

use App\Tests\Controller\BaseController;

final class CommentControllerTest extends BaseController
{
    public function testCommentEditPageWithoutLogin(): void
    {
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('GET', '/user/comment/1/edit');

        $this->assertResponseRedirects('/login');
    }

    public function testCommentEditPageWithUserLoginWithUnexistingComment(): void
    {
        $kernelBrowser = $this->loginUser();
        $kernelBrowser->request('GET', '/user/comment/100/edit');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCommentEditPageWithAdminLoginWithUnexistingComment(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('GET', '/user/comment/100/edit');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCommentEditPageWithUserLoginWithExistingComment(): void
    {
        $kernelBrowser = $this->loginUser();
        $kernelBrowser->request('GET', '/user/comment/1/edit');
        $kernelBrowser->submitForm('Update', [
            'comment[content]' => 'https://www.youtube.com/watch?v=PEP1-Y7fX_I',
        ]);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/');
    }

    public function testCommentEditPageWithAdminLoginWithExistingComment(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('GET', '/user/comment/1/edit');
        $kernelBrowser->submitForm('Update', [
            'comment[content]' => 'https://www.youtube.com/watch?v=PEP1-Y7fX_I',
        ]);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/admin/');
    }

    public function testCommentRemovePageWithoutLogin(): void
    {
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('GET', '/user/comment/1/edit');

        $this->assertResponseRedirects('/login');
    }

    public function testCommentRemovePageWithUserLoginWithUnexistingComment(): void
    {
        $kernelBrowser = $this->loginUser();
        $kernelBrowser->request('GET', '/user/comment/100/edit');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCommentRemovePageWithAdminLoginWithUnexistingComment(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('GET', '/user/comment/100/edit');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCommentRemovePageWithUserLoginWithExistingCommentWithoutAssociation(): void
    {
        $kernelBrowser = $this->loginUser();
        $kernelBrowser->request('GET', '/user/comment/2/edit');
        $kernelBrowser->submitForm($this->trans('app.delete'));

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/');
    }

    public function testCommentRemovePageWithAdminLoginWithExistingCommentWithoutAssociation(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('GET', '/user/comment/2/edit');
        $kernelBrowser->submitForm($this->trans('app.delete'));

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/admin/');
    }

    public function testCommentRemovePageWithUserLoginWithExistingCommentWithAssociation(): void
    {
        $kernelBrowser = $this->loginUser();
        $kernelBrowser->request('GET', '/user/comment/1/edit');
        $kernelBrowser->submitForm($this->trans('app.delete'));

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/');
    }

    public function testCommentRemovePageWithAdminLoginWithExistingCommentWithAssociation(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('GET', '/user/comment/1/edit');
        $kernelBrowser->submitForm($this->trans('app.delete'));

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/admin/');
    }
}
