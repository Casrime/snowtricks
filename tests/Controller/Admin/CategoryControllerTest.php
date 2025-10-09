<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Tests\Controller\BaseController;

final class CategoryControllerTest extends BaseController
{
    public function testCategoryNewPageWithoutLogin(): void
    {
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('GET', '/admin/category/new');

        $this->assertResponseRedirects('/login');
    }

    public function testCategoryNewPageWithUserLogin(): void
    {
        $kernelBrowser = $this->loginUser();
        $kernelBrowser->request('GET', '/admin/category/new');

        $this->assertResponseStatusCodeSame(403);
        $this->assertSelectorTextContains('h1.exception-message', 'Access Denied.');
    }

    public function testCategoryNewPageWithAdminLoginWithoutFormSubmission(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('GET', '/admin/category/new');

        $this->assertResponseStatusCodeSame(200);
        $this->assertPageTitleContains('New Category');
    }

    public function testCategoryNewPageWithAdminLoginWithFormSubmissionWithoutValues(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('GET', '/admin/category/new');
        $kernelBrowser->submitForm('Save');

        $this->assertResponseStatusCodeSame(422);
        $this->assertPageTitleContains('New Category');
        $this->assertSelectorTextContains('#category p.text-red-600', 'This value should not be blank.');
    }

    public function testCategoryNewPageWithAdminLoginWithFormSubmissionWithInvalidValues(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('GET', '/admin/category/new');
        $kernelBrowser->submitForm('Save', [
            'category[name]' => true,
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertPageTitleContains('New Category');
        $this->assertSelectorTextContains('#category p.text-red-600', 'This value is too short. It should have 3 characters or more.');
    }

    public function testCategoryNewPageWithAdminLoginWithFormSubmissionWithValidValues(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('GET', '/admin/category/new');
        $kernelBrowser->submitForm('Save', [
            'category[name]' => 'category created',
        ]);

        $this->assertResponseStatusCodeSame(303);
        $kernelBrowser->followRedirects();
        $this->assertResponseRedirects('/admin/');
    }

    public function testCategoryEditPageWithoutLogin(): void
    {
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('GET', '/admin/category/1/edit');

        $this->assertResponseRedirects('/login');
    }

    public function testCategoryEditPageWithUserLogin(): void
    {
        $kernelBrowser = $this->loginUser();
        $kernelBrowser->request('GET', '/admin/category/1/edit');

        $this->assertResponseStatusCodeSame(403);
        $this->assertSelectorTextContains('h1.exception-message', 'Access Denied.');
    }

    public function testCategoryEditPageWithAdminLoginWithUnexistingCategory(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('GET', '/admin/category/100/edit');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCategoryEditPageWithAdminLoginWithExistingCategory(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('GET', '/admin/category/1/edit');
        $kernelBrowser->submitForm('Update', [
            'category[name]' => 'category updated',
        ]);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/admin/');
    }

    public function testCategoryRemovePageWithoutLogin(): void
    {
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('POST', '/admin/category/1');

        $this->assertResponseRedirects('/login');
    }

    public function testCategoryRemovePageWithUserLogin(): void
    {
        $kernelBrowser = $this->loginUser();
        $kernelBrowser->request('POST', '/admin/category/1');

        $this->assertResponseStatusCodeSame(403);
        $this->assertSelectorTextContains('h1.exception-message', 'Access Denied.');
    }

    public function testCategoryRemovePageWithAdminLoginWithUnexistingCategory(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('POST', '/admin/category/100');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCategoryRemovePageWithAdminLoginWithExistingCategoryWithoutAssociation(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('POST', '/admin/category/4');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/admin/');
    }

    public function testCategoryRemovePageWithAdminLoginWithExistingCategoryWithAssociation(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('POST', '/admin/category/1');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/admin/');
    }
}
