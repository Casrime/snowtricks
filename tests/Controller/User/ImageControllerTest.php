<?php

declare(strict_types=1);

namespace App\Tests\Controller\User;

use App\Tests\Controller\BaseController;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageControllerTest extends BaseController
{
    public function testImageHomePageWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/image/');

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');
    }

    public function testImageHomePageWithUserLogin(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/admin/image/');

        $this->assertResponseStatusCodeSame(403);
        $this->assertSelectorTextContains('h1.exception-message', 'Access Denied.');
    }

    public function testImageHomePageWithAdminLogin(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/admin/image/');

        $this->assertResponseStatusCodeSame(200);
        $this->assertPageTitleContains('Image index');
    }

    public function testImageNewPageWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/image/new');

        $this->assertResponseRedirects('/login');
    }

    public function testImageNewPageWithUserLogin(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/admin/image/new');

        $this->assertResponseStatusCodeSame(403);
        $this->assertSelectorTextContains('h1.exception-message', 'Access Denied.');
    }

    public function testImageNewPageWithAdminLoginWithoutFormSubmission(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/admin/image/new');

        $this->assertResponseStatusCodeSame(200);
        $this->assertPageTitleContains('New Image');
    }

    public function testImageNewPageWithAdminLoginWithFormSubmissionWithoutValues(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/admin/image/new');
        $client->submitForm('Save');

        $this->assertResponseStatusCodeSame(422);
        $this->assertPageTitleContains('New Image');
        $this->assertSelectorTextContains('li', 'This value should not be blank.');
    }

    public function testImageNewPageWithAdminLoginWithFormSubmissionWithInvalidValues(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/admin/image/new');
        $client->submitForm('Save', [
            'image[name]' => new UploadedFile(
                __DIR__.'/../../public/uploads/images/snowtricks.txt',
                'snowtricks.txt'
            ),
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertPageTitleContains('New Image');
        $this->assertSelectorTextContains('li', 'Please upload a valid image');
    }

    public function testImageNewPageWithAdminLoginWithFormSubmissionWithValidValues(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/admin/image/new');
        $client->submitForm('Save', [
            'image[name]' => new UploadedFile(
                __DIR__.'/../../public/uploads/images/snowtricks.jpg',
                'snowtricks.jpg'
            ),
            'image[alt]' => 'Testing',
        ]);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/admin/image/');
        $this->assertPageTitleContains('Redirecting to /admin/image/');
    }

    public function testImageShowPageWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/image/1');

        $this->assertResponseRedirects('/login');
    }

    public function testImageShowPageWithUserLogin(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/admin/image/1');

        $this->assertResponseStatusCodeSame(403);
        $this->assertSelectorTextContains('h1.exception-message', 'Access Denied.');
    }

    public function testImageShowPageWithAdminLogin(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/admin/image/1');

        $this->assertResponseStatusCodeSame(200);
        $this->assertPageTitleContains('Image');
        $this->assertSelectorTextContains('h1', 'Image');
    }

    public function testImageEditPageWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/image/1/edit');

        $this->assertResponseRedirects('/login');
    }

    public function testImageEditPageWithUserLogin(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/admin/image/1/edit');

        $this->assertResponseStatusCodeSame(403);
        $this->assertSelectorTextContains('h1.exception-message', 'Access Denied.');
    }

    public function testImageEditPageWithAdminLoginWithUnexistingImage(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/admin/image/100/edit');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testImageEditPageWithAdminLoginWithExistingImage(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/admin/image/1/edit');
        $client->submitForm('Update', [
            'image[name]' => new UploadedFile(
                __DIR__.'/../../public/uploads/images/snowtricks-2.jpg',
                'snowtricks-2.jpg'
            ),
        ]);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/admin/image/');
        $this->assertPageTitleContains('Redirecting to /admin/image/');
    }

    public function testImageRemovePageWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/image/1');

        $this->assertResponseRedirects('/login');
    }

    public function testImageRemovePageWithUserLogin(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/admin/image/1');

        $this->assertResponseStatusCodeSame(403);
        $this->assertSelectorTextContains('h1.exception-message', 'Access Denied.');
    }

    public function testImageRemovePageWithAdminLoginWithUnexistingImage(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/admin/image/100');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testImageRemovePageWithAdminLoginWithExistingImageWithoutAssociation(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/admin/image/3');
        $client->submitForm('Delete');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/admin/image/');
        $this->assertPageTitleContains('Redirecting to /admin/image/');
    }

    public function testImageRemovePageWithAdminLoginWithExistingImageWithAssociation(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/admin/image/1');
        $client->submitForm('Delete');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/admin/image/');
        $this->assertPageTitleContains('Redirecting to /admin/image/');
    }
}
