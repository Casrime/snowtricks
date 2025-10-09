<?php

declare(strict_types=1);

namespace App\Tests\Controller\User;

use App\Tests\Controller\BaseController;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class ImageControllerTest extends BaseController
{
    public function testImageNewPageWithoutLogin(): void
    {
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('GET', '/user/image/new');

        $this->assertResponseRedirects('/login');
    }

    public function testImageNewPageWithUserLogin(): void
    {
        $kernelBrowser = $this->loginUser();
        $kernelBrowser->request('GET', '/user/image/new');

        $this->assertResponseStatusCodeSame(200);
    }

    public function testImageNewPageWithAdminLoginWithoutFormSubmission(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('GET', '/user/image/new');

        $this->assertResponseStatusCodeSame(200);
        $this->assertPageTitleContains('New Image');
    }

    public function testImageNewPageWithAdminLoginWithFormSubmissionWithoutValues(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('GET', '/user/image/new');
        $kernelBrowser->submitForm('Save');

        $this->assertResponseStatusCodeSame(422);
        $this->assertPageTitleContains('New Image');
        $this->assertSelectorTextContains('#image_name_error_0', 'This value should not be blank.');
    }

    public function testImageNewPageWithAdminLoginWithFormSubmissionWithInvalidValues(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('GET', '/user/image/new');
        $kernelBrowser->submitForm('Save', [
            'image[name]' => new UploadedFile(
                __DIR__.'/../../../public/uploads/images/snowtricks.txt',
                'snowtricks.txt'
            ),
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertPageTitleContains('New Image');
        $this->assertSelectorTextContains('#image_name_error_0', 'Please upload a valid image');
    }

    public function testImageNewPageWithAdminLoginWithFormSubmissionWithValidValues(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('GET', '/user/image/new');
        $kernelBrowser->submitForm('Save', [
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
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('GET', '/user/image/1/edit');

        $this->assertResponseRedirects('/login');
    }

    public function testImageEditPageWithUserLogin(): void
    {
        $kernelBrowser = $this->loginUser();
        $kernelBrowser->request('GET', '/user/image/1/edit');

        $this->assertResponseStatusCodeSame(200);
    }

    public function testImageEditPageWithAdminLoginWithUnexistingImage(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('GET', '/user/image/100/edit');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testImageEditPageWithAdminLoginWithExistingImage(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('GET', '/user/image/1/edit');
        $kernelBrowser->submitForm('Update', [
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
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('POST', '/user/image/1');

        $this->assertResponseRedirects('/login');
    }

    public function testImageRemovePageWithUserLogin(): void
    {
        $kernelBrowser = $this->loginUser();
        $kernelBrowser->request('POST', '/user/image/1');

        $this->assertResponseStatusCodeSame(303);
    }

    public function testImageRemovePageWithAdminLoginWithUnexistingImage(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('POST', '/user/image/100');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testImageRemovePageWithAdminLoginWithExistingImageWithoutAssociation(): void
    {
        $kernelBrowser = $this->loginUser();
        $kernelBrowser->request('POST', '/user/image/3');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/');
    }

    public function testImageRemovePageWithAdminLoginWithExistingImageWithAssociation(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('POST', '/user/image/1');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/admin/');
    }
}
