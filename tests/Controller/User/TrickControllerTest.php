<?php

namespace App\Tests\Controller\User;

use App\Tests\Controller\BaseController;

class TrickControllerTest extends BaseController
{
    public function testTrickHomePageWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/user/trick/');

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');
    }

    public function testTrickHomePageWithUserLogin(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/user/trick/');

        $this->assertResponseStatusCodeSame(200);
        $this->assertPageTitleContains('Trick index');
    }

    public function testTrickHomePageWithAdminLogin(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/trick/');

        $this->assertResponseStatusCodeSame(200);
        $this->assertPageTitleContains('Trick index');
    }

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
        $this->assertSelectorTextContains('li', 'This value should not be blank.');
    }

    public function testTrickNewPageWithAdminLoginWithFormSubmissionWithInvalidValues(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/trick/new');
        $client->submitForm('Save', [
            'trick[name]' => true,
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertPageTitleContains('New Trick');
        $this->assertSelectorTextContains('li', 'This value should not be blank.');
    }

    public function testTrickNewPageWithAdminLoginWithFormSubmissionWithMinimalValidValues(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/trick/new');
        $client->submitForm('Save', [
            'trick[name]' => 'New trick',
            'trick[category]' => 1,
        ]);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/trick/');
        $this->assertPageTitleContains('Redirecting to /user/trick/');
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
        $this->assertResponseRedirects('/user/trick/');
        $this->assertPageTitleContains('Redirecting to /user/trick/');
    }

    public function testTrickShowPageWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/user/trick/1');

        $this->assertResponseRedirects('/login');
    }

    public function testTrickShowPageWithUserLogin(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/user/trick/1');

        $this->assertPageTitleContains('Trick');
        $this->assertSelectorTextContains('h1', 'Trick');
    }

    public function testTrickShowPageWithAdminLogin(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/trick/1');

        $this->assertResponseStatusCodeSame(200);
        $this->assertPageTitleContains('Trick');
        $this->assertSelectorTextContains('h1', 'Trick');
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
        $this->assertResponseRedirects('/user/trick/');
        $this->assertPageTitleContains('Redirecting to /user/trick/');
    }

    public function testTrickEditPageWithAdminLoginWithExistingTrick(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/trick/1/edit');
        $client->submitForm('Update', [
            'trick[name]' => 'https://www.youtube.com/watch?v=PEP1-Y7fX_I',
        ]);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/trick/');
        $this->assertPageTitleContains('Redirecting to /user/trick/');
    }

    public function testTrickRemovePageWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/user/trick/1');

        $this->assertResponseRedirects('/login');
    }

    public function testTrickRemovePageWithUserLoginWithUnexistingTrick(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/user/trick/100');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testTrickRemovePageWithAdminLoginWithUnexistingTrick(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/trick/100');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testTrickRemovePageWithUserLoginWithExistingTrickWithoutAssociation(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/user/trick/2');
        $client->submitForm('Delete');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/trick/');
        $this->assertPageTitleContains('Redirecting to /user/trick/');
    }

    public function testTrickRemovePageWithAdminLoginWithExistingTrickWithoutAssociation(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/trick/2');
        $client->submitForm('Delete');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/trick/');
        $this->assertPageTitleContains('Redirecting to /user/trick/');
    }

    public function testTrickRemovePageWithUserLoginWithExistingTrickWithAssociation(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/user/trick/1');
        $client->submitForm('Delete');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/trick/');
        $this->assertPageTitleContains('Redirecting to /user/trick/');
    }

    public function testTrickRemovePageWithAdminLoginWithExistingTrickWithAssociation(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/user/trick/1');
        $client->submitForm('Delete');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/trick/');
        $this->assertPageTitleContains('Redirecting to /user/trick/');
    }

    /*
    private string $path = '/trick/';

    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Trick index');
    }

    public function testNew(): void
    {
        $client = static::createClient();
        $client->request('GET', '/trick/new');

        self::assertResponseStatusCodeSame(200);

        $client->submitForm('Save', [
            'trick[name]' => 'Testing',
            'trick[description]' => 'Testing',
            'trick[category]' => '1',
        ]);

        self::assertResponseRedirects('/trick/');
    }

    public function testShow(): void
    {
        $client = static::createClient();
        $client->request('GET', '/trick/1');

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Trick');
    }

    public function testEdit(): void
    {
        $client = static::createClient();
        $client->request('GET', '/trick/1/edit');

        $client->submitForm('Update', [
            'trick[name]' => 'Something New',
            'trick[description]' => 'Something New',
            'trick[category]' => '2',
        ]);

        self::assertResponseRedirects('/trick/');
    }

    public function testRemove(): void
    {
        $client = static::createClient();
        $client->request('GET', '/trick/1');
        $client->submitForm('Delete');

        self::assertResponseRedirects('/trick/');
    }
    */
}
