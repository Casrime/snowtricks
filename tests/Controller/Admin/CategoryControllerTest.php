<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Tests\Controller\BaseController;
use Symfony\Component\DomCrawler\Link;

class CategoryControllerTest extends BaseController
{
    public function testCategoryNewPageWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/category/new');

        $this->assertResponseRedirects('/login');
    }

    public function testCategoryNewPageWithUserLogin(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/admin/category/new');

        $this->assertResponseStatusCodeSame(403);
        $this->assertSelectorTextContains('h1.exception-message', 'Access Denied.');
    }

    public function testCategoryNewPageWithAdminLoginWithoutFormSubmission(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/admin/category/new');

        $this->assertResponseStatusCodeSame(200);
        $this->assertPageTitleContains('New Category');
    }

    public function testCategoryNewPageWithAdminLoginWithFormSubmissionWithoutValues(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/admin/category/new');
        $client->submitForm('Save');

        $this->assertResponseStatusCodeSame(422);
        $this->assertPageTitleContains('New Category');
        $this->assertSelectorTextContains('.invalid-feedback', 'This value should not be blank.');
    }

    public function testCategoryNewPageWithAdminLoginWithFormSubmissionWithInvalidValues(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/admin/category/new');
        $client->submitForm('Save', [
            'category[name]' => true,
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertPageTitleContains('New Category');
        $this->assertSelectorTextContains('.invalid-feedback', 'This value is too short. It should have 3 characters or more.');
    }

    public function testCategoryNewPageWithAdminLoginWithFormSubmissionWithValidValues(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/admin/category/new');
        $client->submitForm('Save', [
            'category[name]' => 'category created',
        ]);

        $this->assertResponseStatusCodeSame(303);
        $client->followRedirects();
        $this->assertResponseRedirects('/admin/');
    }

    public function testCategoryEditPageWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/category/1/edit');

        $this->assertResponseRedirects('/login');
    }

    public function testCategoryEditPageWithUserLogin(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/admin/category/1/edit');

        $this->assertResponseStatusCodeSame(403);
        $this->assertSelectorTextContains('h1.exception-message', 'Access Denied.');
    }

    public function testCategoryEditPageWithAdminLoginWithUnexistingCategory(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/admin/category/100/edit');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCategoryEditPageWithAdminLoginWithExistingCategory(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/admin/category/1/edit');
        $client->submitForm('Update', [
            'category[name]' => 'category updated',
        ]);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/admin/');
    }

    public function testCategoryRemovePageWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('POST', '/admin/category/1');

        $this->assertResponseRedirects('/login');
    }

    public function testCategoryRemovePageWithUserLogin(): void
    {
        $client = $this->loginUser();
        $client->request('POST', '/admin/category/1');

        $this->assertResponseStatusCodeSame(403);
        $this->assertSelectorTextContains('h1.exception-message', 'Access Denied.');
    }

    public function testCategoryRemovePageWithAdminLoginWithUnexistingCategory(): void
    {
        $client = $this->loginAdmin();
        $client->request('POST', '/admin/category/100');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCategoryRemovePageWithAdminLoginWithExistingCategoryWithoutAssociation(): void
    {
        $client = $this->loginAdmin();
        $client->request('POST', '/admin/category/4');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/admin/');
    }

    public function testCategoryRemovePageWithAdminLoginWithExistingCategoryWithAssociation(): void
    {
        $client = $this->loginAdmin();
        $client->request('POST', '/admin/category/1');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/admin/');
    }
}
