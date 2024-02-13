<?php

namespace App\Tests\Controller\User;

use App\Tests\Controller\BaseController;

class TrickControllerTest extends BaseController
{
    public function testTrickNewPageWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/user/trick/new');

        $this->assertResponseRedirects('/login');
    }

    public function testTrickNewPageWithUserLogin(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/user/trick/new');

        $this->assertResponseStatusCodeSame(200);
        $this->assertPageTitleContains('New Trick');
    }

    public function testTrickNewPageWithAdminLoginWithoutFormSubmission(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/trick/new');

        $this->assertResponseStatusCodeSame(200);
        $this->assertPageTitleContains('New Trick');
    }

    public function testTrickNewPageWithAdminLoginWithFormSubmissionWithoutValues(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/trick/new');
        $client->submitForm('Save');

        $this->assertResponseStatusCodeSame(422);
        $this->assertPageTitleContains('New Trick');
        $this->assertSelectorTextContains('.invalid-feedback', 'This value should not be blank.');
    }

    public function testTrickNewPageWithAdminLoginWithFormSubmissionWithInvalidValues(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/trick/new');
        $client->submitForm('Save', [
            'trick[name]' => null,
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertPageTitleContains('New Trick');
        $this->assertSelectorTextContains('.invalid-feedback', 'This value should not be blank.');
    }

    public function testTrickNewPageWithUserLoginWithFormSubmissionWithMinimalValidValues(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/user/trick/new');
        $client->submitForm('Save', [
            'trick[name]' => 'New trick',
            'trick[category]' => 1,
        ]);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/');
    }

    public function testTrickNewPageWithAdminLoginWithFormSubmissionWithMaximalValidValues(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/trick/new');
        $client->submitForm('Save', [
            'trick[name]' => 'New trick',
            'trick[category]' => 1,
            'trick[description]' => 'New trick description',
            'trick[images]' => [1, 2, 3],
            'trick[videos]' => [1, 2],
        ]);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/admin/');
    }

    public function testTrickEditPageWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/user/trick/1/edit');

        $this->assertResponseRedirects('/login');
    }

    public function testTrickEditPageWithUserLoginWithUnexistingTrick(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/user/trick/100/edit');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testTrickEditPageWithAdminLoginWithUnexistingTrick(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/trick/100/edit');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testTrickEditPageWithUserLoginWithExistingTrick(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/user/trick/1/edit');
        $client->submitForm('Update', [
            'trick[name]' => 'https://www.youtube.com/watch?v=PEP1-Y7fX_I',
        ]);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/');
    }

    public function testTrickEditPageWithAdminLoginWithExistingTrick(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/trick/1/edit');
        $client->submitForm('Update', [
            'trick[name]' => 'https://www.youtube.com/watch?v=PEP1-Y7fX_I',
        ]);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/admin/');
    }

    public function testTrickRemovePageWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('POST', '/user/trick/1');

        $this->assertResponseRedirects('/login');
    }

    public function testTrickRemovePageWithUserLoginWithUnexistingTrick(): void
    {
        $client = $this->loginUser();
        $client->request('POST', '/user/trick/100');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testTrickRemovePageWithAdminLoginWithUnexistingTrick(): void
    {
        $client = $this->loginAdmin();
        $client->request('POST', '/user/trick/100');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testTrickRemovePageWithUserLoginWithExistingTrickWithoutAssociation(): void
    {
        $client = $this->loginUser();
        $client->request('POST', '/user/trick/2');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/');
    }

    public function testTrickRemovePageWithAdminLoginWithExistingTrickWithoutAssociation(): void
    {
        $client = $this->loginAdmin();
        $client->request('POST', '/user/trick/2');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/admin/');
    }

    public function testTrickRemovePageWithUserLoginWithExistingTrickWithAssociation(): void
    {
        $client = $this->loginUser();
        $client->request('POST', '/user/trick/1');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/');
    }

    public function testTrickRemovePageWithAdminLoginWithExistingTrickWithAssociation(): void
    {
        $client = $this->loginAdmin();
        $client->request('POST', '/user/trick/1');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/admin/');
    }
}
