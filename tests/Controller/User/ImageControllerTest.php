<?php

declare(strict_types=1);

namespace App\Tests\Controller\User;

use App\Tests\Controller\BaseController;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageControllerTest extends BaseController
{
    public function testImageNewPageWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/user/image/new');

        $this->assertResponseRedirects('/login');
    }

    public function testImageNewPageWithUserLogin(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/user/image/new');

        $this->assertResponseStatusCodeSame(200);
    }

    public function testImageNewPageWithAdminLoginWithoutFormSubmission(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/image/new');

        $this->assertResponseStatusCodeSame(200);
        $this->assertPageTitleContains('New Image');
    }

    public function testImageNewPageWithAdminLoginWithFormSubmissionWithoutValues(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/image/new');
        $client->submitForm('Save');

        $this->assertResponseStatusCodeSame(422);
        $this->assertPageTitleContains('New Image');
        $this->assertSelectorTextContains('.invalid-feedback', 'This value should not be blank.');
    }

    public function testImageNewPageWithAdminLoginWithFormSubmissionWithInvalidValues(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/image/new');
        $client->submitForm('Save', [
            'image[name]' => new UploadedFile(
                __DIR__.'/../../../public/uploads/images/snowtricks.txt',
                'snowtricks.txt'
            ),
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertPageTitleContains('New Image');
        $this->assertSelectorTextContains('.invalid-feedback', 'Please upload a valid image');
    }

    public function testImageNewPageWithAdminLoginWithFormSubmissionWithValidValues(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/image/new');
        $client->submitForm('Save', [
            'image[name]' => new UploadedFile(
                __DIR__.'/../../../public/uploads/images/snowtricks.jpg',
                'snowtricks.jpg'
            ),
            'image[alt]' => 'Testing',
        ]);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/admin/');
    }

    public function testImageEditPageWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/user/image/1/edit');

        $this->assertResponseRedirects('/login');
    }

    public function testImageEditPageWithUserLogin(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/user/image/1/edit');

        $this->assertResponseStatusCodeSame(200);
    }

    public function testImageEditPageWithAdminLoginWithUnexistingImage(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/image/100/edit');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testImageEditPageWithAdminLoginWithExistingImage(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/image/1/edit');
        $client->submitForm('Update', [
            'image[name]' => new UploadedFile(
                __DIR__.'/../../../public/uploads/images/snowtricks-2.jpg',
                'snowtricks-2.jpg'
            ),
        ]);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/admin/');
    }

    public function testImageRemovePageWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('POST', '/user/image/1');

        $this->assertResponseRedirects('/login');
    }

    public function testImageRemovePageWithUserLogin(): void
    {
        $client = $this->loginUser();
        $client->request('POST', '/user/image/1');

        $this->assertResponseStatusCodeSame(303);
    }

    public function testImageRemovePageWithAdminLoginWithUnexistingImage(): void
    {
        $client = $this->loginAdmin();
        $client->request('POST', '/user/image/100');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testImageRemovePageWithAdminLoginWithExistingImageWithoutAssociation(): void
    {
        $client = $this->loginUser();
        $client->request('POST', '/user/image/3');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/');
    }

    public function testImageRemovePageWithAdminLoginWithExistingImageWithAssociation(): void
    {
        $client = $this->loginAdmin();
        $client->request('POST', '/user/image/1');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/admin/');
    }
}
