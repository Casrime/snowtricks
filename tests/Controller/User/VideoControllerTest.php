<?php

declare(strict_types=1);

namespace App\Tests\Controller\User;

use App\Tests\Controller\BaseController;

class VideoControllerTest extends BaseController
{
    public function testVideoHomePageWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/user/video/');

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');
    }

    public function testVideoHomePageWithUserLogin(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/user/video/');

        $this->assertResponseStatusCodeSame(200);
        $this->assertPageTitleContains('Video index');
    }

    public function testVideoHomePageWithAdminLogin(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/video/');

        $this->assertResponseStatusCodeSame(200);
        $this->assertPageTitleContains('Video index');
    }

    public function testVideoNewPageWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/user/video/new');

        $this->assertResponseRedirects('/login');
    }

    public function testVideoNewPageWithUserLogin(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/user/video/new');

        $this->assertResponseStatusCodeSame(200);
        $this->assertPageTitleContains('New Video');
    }

    public function testVideoNewPageWithAdminLoginWithoutFormSubmission(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/video/new');

        $this->assertResponseStatusCodeSame(200);
        $this->assertPageTitleContains('New Video');
    }

    public function testVideoNewPageWithAdminLoginWithFormSubmissionWithoutValues(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/video/new');
        $client->submitForm('Save');

        $this->assertResponseStatusCodeSame(422);
        $this->assertPageTitleContains('New Video');
        $this->assertSelectorTextContains('li', 'This value should not be blank.');
    }

    public function testVideoNewPageWithAdminLoginWithFormSubmissionWithInvalidValues(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/video/new');
        $client->submitForm('Save', [
            'video[url]' => 'video',
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertPageTitleContains('New Video');
        $this->assertSelectorTextContains('li', 'This value is not a valid URL.');
    }

    public function testVideoNewPageWithAdminLoginWithFormSubmissionWithValidValues(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/video/new');
        $client->submitForm('Save', [
            'video[url]' => 'https://www.youtube.com/watch?v=xsE5sFZ3sq0',
        ]);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/video/');
        $this->assertPageTitleContains('Redirecting to /user/video/');
    }

    public function testVideoShowPageWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/user/video/1');

        $this->assertResponseRedirects('/login');
    }

    public function testVideoShowPageWithUserLogin(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/user/video/1');

        $this->assertPageTitleContains('Video');
        $this->assertSelectorTextContains('h1', 'Video');
    }

    public function testVideoShowPageWithAdminLogin(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/video/1');

        $this->assertResponseStatusCodeSame(200);
        $this->assertPageTitleContains('Video');
        $this->assertSelectorTextContains('h1', 'Video');
    }

    public function testVideoEditPageWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/user/video/1/edit');

        $this->assertResponseRedirects('/login');
    }

    public function testVideoEditPageWithUserLoginWithUnexistingVideo(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/user/video/100/edit');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testVideoEditPageWithAdminLoginWithUnexistingVideo(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/video/100/edit');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testVideoEditPageWithUserLoginWithExistingVideo(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/user/video/1/edit');
        $client->submitForm('Update', [
            'video[url]' => 'https://www.youtube.com/watch?v=PEP1-Y7fX_I',
        ]);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/video/');
        $this->assertPageTitleContains('Redirecting to /user/video/');
    }

    public function testVideoEditPageWithAdminLoginWithExistingVideo(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/video/1/edit');
        $client->submitForm('Update', [
            'video[url]' => 'https://www.youtube.com/watch?v=PEP1-Y7fX_I',
        ]);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/video/');
        $this->assertPageTitleContains('Redirecting to /user/video/');
    }

    public function testVideoRemovePageWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/user/video/1');

        $this->assertResponseRedirects('/login');
    }

    public function testVideoRemovePageWithUserLoginWithUnexistingVideo(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/user/video/100');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testVideoRemovePageWithAdminLoginWithUnexistingVideo(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/video/100');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testVideoRemovePageWithUserLoginWithExistingVideoWithoutAssociation(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/user/video/2');
        $client->submitForm('Delete');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/video/');
        $this->assertPageTitleContains('Redirecting to /user/video/');
    }

    public function testVideoRemovePageWithAdminLoginWithExistingVideoWithoutAssociation(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/video/2');
        $client->submitForm('Delete');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/video/');
        $this->assertPageTitleContains('Redirecting to /user/video/');
    }

    public function testVideoRemovePageWithUserLoginWithExistingVideoWithAssociation(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/user/video/1');
        $client->submitForm('Delete');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/video/');
        $this->assertPageTitleContains('Redirecting to /user/video/');
    }

    public function testVideoRemovePageWithAdminLoginWithExistingVideoWithAssociation(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/video/1');
        $client->submitForm('Delete');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/video/');
        $this->assertPageTitleContains('Redirecting to /user/video/');
    }
}
