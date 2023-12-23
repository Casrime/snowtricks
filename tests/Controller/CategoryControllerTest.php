<?php

declare(strict_types=1);

namespace App\Tests\Controller;

class CategoryControllerTest extends BaseController
{
    public function testCategoryHomePageWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/category/');

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');
    }

    public function testCategoryHomePageWithUserLogin(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/admin/category/');

        $this->assertResponseStatusCodeSame(403);
        $this->assertSelectorTextContains('h1.exception-message', 'Access Denied.');
    }

    public function testCategoryHomePageWithAdminLogin(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/admin/category/');

        $this->assertResponseStatusCodeSame(200);
        $this->assertPageTitleContains('Category index');
    }

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
        $this->assertSelectorTextContains('li', 'This value should not be blank.');
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
        $this->assertSelectorTextContains('li', 'This value is too short. It should have 3 characters or more.');
    }

    public function testCategoryNewPageWithAdminLoginWithFormSubmissionWithValidValues(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/admin/category/new');
        $client->submitForm('Save', [
            'category[name]' => 'category created',
        ]);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/admin/category/');
        $this->assertPageTitleContains('Redirecting to /admin/category/');
    }

    public function testCategoryShowPageWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/category/1');

        $this->assertResponseRedirects('/login');
    }

    public function testCategoryShowPageWithUserLogin(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/admin/category/1');

        $this->assertResponseStatusCodeSame(403);
        $this->assertSelectorTextContains('h1.exception-message', 'Access Denied.');
    }

    public function testCategoryShowPageWithAdminLogin(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/admin/category/1');

        $this->assertResponseStatusCodeSame(200);
        $this->assertPageTitleContains('Category');
        $this->assertSelectorTextContains('h1', 'Category');
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
        $this->assertResponseRedirects('/admin/category/');
        $this->assertPageTitleContains('Redirecting to /admin/category/');
    }

    public function testCategoryRemovePageWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/category/1');

        $this->assertResponseRedirects('/login');
    }

    public function testCategoryRemovePageWithUserLogin(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/admin/category/1');

        $this->assertResponseStatusCodeSame(403);
        $this->assertSelectorTextContains('h1.exception-message', 'Access Denied.');
    }

    public function testCategoryRemovePageWithAdminLoginWithUnexistingCategory(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/admin/category/100');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCategoryRemovePageWithAdminLoginWithExistingCategoryWithoutAssociation(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/admin/category/4');
        $client->submitForm('Delete');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/admin/category/');
        $this->assertPageTitleContains('Redirecting to /admin/category/');
    }

    public function testCategoryRemovePageWithAdminLoginWithExistingCategoryWithAssociation(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/admin/category/1');
        $client->submitForm('Delete');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/admin/category/');
        $this->assertPageTitleContains('Redirecting to /admin/category/');
    }
}
