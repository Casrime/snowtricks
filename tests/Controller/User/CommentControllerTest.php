<?php

declare(strict_types=1);

namespace App\Tests\Controller\User;

use App\Tests\Controller\BaseController;

class CommentControllerTest extends BaseController
{
    public function testCommentEditPageWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/user/comment/1/edit');

        $this->assertResponseRedirects('/login');
    }

    public function testCommentEditPageWithUserLoginWithUnexistingComment(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/user/comment/100/edit');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCommentEditPageWithAdminLoginWithUnexistingComment(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/comment/100/edit');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCommentEditPageWithUserLoginWithExistingComment(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/user/comment/1/edit');
        $client->submitForm('Update', [
            'comment[content]' => 'https://www.youtube.com/watch?v=PEP1-Y7fX_I',
        ]);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/');
    }

    public function testCommentEditPageWithAdminLoginWithExistingComment(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/comment/1/edit');
        $client->submitForm('Update', [
            'comment[content]' => 'https://www.youtube.com/watch?v=PEP1-Y7fX_I',
        ]);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/');
    }

    public function testCommentRemovePageWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/user/comment/1/edit');

        $this->assertResponseRedirects('/login');
    }

    public function testCommentRemovePageWithUserLoginWithUnexistingComment(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/user/comment/100/edit');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCommentRemovePageWithAdminLoginWithUnexistingComment(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/comment/100/edit');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCommentRemovePageWithUserLoginWithExistingCommentWithoutAssociation(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/user/comment/2/edit');
        $client->submitForm('Delete');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/');
    }

    public function testCommentRemovePageWithAdminLoginWithExistingCommentWithoutAssociation(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/comment/2/edit');
        $client->submitForm('Delete');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/');
    }

    public function testCommentRemovePageWithUserLoginWithExistingCommentWithAssociation(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/user/comment/1/edit');
        $client->submitForm('Delete');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/');
    }

    public function testCommentRemovePageWithAdminLoginWithExistingCommentWithAssociation(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/comment/1/edit');
        $client->submitForm('Delete');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/');
    }
}
