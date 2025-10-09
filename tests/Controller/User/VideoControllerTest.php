<?php

declare(strict_types=1);

namespace App\Tests\Controller\User;

use App\Tests\Controller\BaseController;

final class VideoControllerTest extends BaseController
{
    public function testVideoNewPageWithoutLogin(): void
    {
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('GET', '/user/video/new');

        $this->assertResponseRedirects('/login');
    }

    public function testVideoNewPageWithUserLogin(): void
    {
        $kernelBrowser = $this->loginUser();
        $kernelBrowser->request('GET', '/user/video/new');

        $this->assertResponseStatusCodeSame(200);
        $this->assertPageTitleContains('New Video');
    }

    public function testVideoNewPageWithAdminLoginWithoutFormSubmission(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('GET', '/user/video/new');

        $this->assertResponseStatusCodeSame(200);
        $this->assertPageTitleContains('New Video');
    }

    public function testVideoNewPageWithAdminLoginWithFormSubmissionWithoutValues(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('GET', '/user/video/new');
        $kernelBrowser->submitForm('Save');

        $this->assertResponseStatusCodeSame(422);
        $this->assertPageTitleContains('New Video');
        $this->assertSelectorTextContains('#video_url_error_0', 'This value should not be blank.');
    }

    public function testVideoNewPageWithAdminLoginWithFormSubmissionWithInvalidValues(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('GET', '/user/video/new');
        $kernelBrowser->submitForm('Save', [
            'video[url]' => 'video',
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertPageTitleContains('New Video');
        $this->assertSelectorTextContains('#video_url_error_0', 'This value is not a valid URL.');
    }

    public function testVideoNewPageWithUserLoginWithFormSubmissionWithValidValues(): void
    {
        $kernelBrowser = $this->loginUser();
        $kernelBrowser->request('GET', '/user/video/new');
        $kernelBrowser->submitForm('Save', [
            'video[url]' => 'https://www.youtube.com/watch?v=xsE5sFZ3sq0',
        ]);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/');
    }

    public function testVideoNewPageWithAdminLoginWithFormSubmissionWithValidValues(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('GET', '/user/video/new');
        $kernelBrowser->submitForm('Save', [
            'video[url]' => 'https://www.youtube.com/watch?v=xsE5sFZ3sq0',
        ]);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/admin/');
    }

    public function testVideoEditPageWithoutLogin(): void
    {
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('GET', '/user/video/1/edit');

        $this->assertResponseRedirects('/login');
    }

    public function testVideoEditPageWithUserLoginWithUnexistingVideo(): void
    {
        $kernelBrowser = $this->loginUser();
        $kernelBrowser->request('GET', '/user/video/100/edit');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testVideoEditPageWithAdminLoginWithUnexistingVideo(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('GET', '/user/video/100/edit');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testVideoEditPageWithUserLoginWithExistingVideo(): void
    {
        $kernelBrowser = $this->loginUser();
        $kernelBrowser->request('GET', '/user/video/1/edit');
        $kernelBrowser->submitForm('Update', [
            'video[url]' => 'https://www.youtube.com/watch?v=PEP1-Y7fX_I',
        ]);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/');
    }

    public function testVideoEditPageWithAdminLoginWithExistingVideo(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('GET', '/user/video/1/edit');
        $kernelBrowser->submitForm('Update', [
            'video[url]' => 'https://www.youtube.com/watch?v=PEP1-Y7fX_I',
        ]);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/admin/');
    }

    public function testVideoRemovePageWithoutLogin(): void
    {
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('POST', '/user/video/1');

        $this->assertResponseRedirects('/login');
    }

    public function testVideoRemovePageWithUserLoginWithUnexistingVideo(): void
    {
        $kernelBrowser = $this->loginUser();
        $kernelBrowser->request('POST', '/user/video/100');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testVideoRemovePageWithAdminLoginWithUnexistingVideo(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('POST', '/user/video/100');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testVideoRemovePageWithUserLoginWithExistingVideoWithoutAssociation(): void
    {
        $kernelBrowser = $this->loginUser();
        $kernelBrowser->request('POST', '/user/video/2');
        // $client->submitForm('Delete');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/');
        // $this->assertPageTitleContains('Redirecting to /user/video/');
    }

    public function testVideoRemovePageWithAdminLoginWithExistingVideoWithoutAssociation(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('POST', '/user/video/2');
        // $client->submitForm('Delete');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/admin/');
    }

    public function testVideoRemovePageWithUserLoginWithExistingVideoWithAssociation(): void
    {
        $kernelBrowser = $this->loginUser();
        $kernelBrowser->request('POST', '/user/video/1');
        // $client->submitForm('Delete');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/');
    }

    public function testVideoRemovePageWithAdminLoginWithExistingVideoWithAssociation(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('POST', '/user/video/1');
        // $client->submitForm('Delete');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/admin/');
    }
}
