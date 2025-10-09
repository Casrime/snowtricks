<?php

declare(strict_types=1);

namespace App\Tests\Controller\User;

use App\Entity\Trick;
use App\Tests\Controller\BaseController;

final class TrickControllerTest extends BaseController
{
    public function testTrickNewPageWithoutLogin(): void
    {
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('GET', '/user/trick/new');

        $this->assertResponseRedirects('/login');
    }

    public function testTrickNewPageWithUserLogin(): void
    {
        $kernelBrowser = $this->loginUser();
        $kernelBrowser->request('GET', '/user/trick/new');

        $this->assertResponseStatusCodeSame(200);
        $this->assertPageTitleContains('New Trick');
    }

    public function testTrickNewPageWithAdminLoginWithoutFormSubmission(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('GET', '/user/trick/new');

        $this->assertResponseStatusCodeSame(200);
        $this->assertPageTitleContains('New Trick');
    }

    public function testTrickNewPageWithAdminLoginWithFormSubmissionWithoutValues(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('GET', '/user/trick/new');
        $kernelBrowser->submitForm('Save');

        $this->assertResponseStatusCodeSame(422);
        $this->assertPageTitleContains('New Trick');
        $this->assertSelectorTextContains('#trick_name_error_0', 'This value should not be blank.');
    }

    public function testTrickNewPageWithAdminLoginWithFormSubmissionWithInvalidValues(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('GET', '/user/trick/new');
        $kernelBrowser->submitForm('Save', [
            'trick[name]' => null,
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertPageTitleContains('New Trick');
        $this->assertSelectorTextContains('#trick_name_error_0', 'This value should not be blank.');
    }

    public function testTrickNewPageWithUserLoginWithFormSubmissionWithMinimalValidValues(): void
    {
        $kernelBrowser = $this->loginUser();
        $kernelBrowser->request('GET', '/user/trick/new');
        $kernelBrowser->submitForm('Save', [
            'trick[name]' => 'New trick',
            'trick[category]' => 1,
        ]);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/');

        $container = self::getContainer();
        /** @var Trick $trick */
        $trick = $container->get('doctrine')->getManager()->getRepository(Trick::class)->findOneBy(['name' => 'New trick']);
        $kernelBrowser->request('GET', '/trick/'.$trick->getId());
        $this->assertResponseStatusCodeSame(200);
    }

    public function testTrickNewPageWithAdminLoginWithFormSubmissionWithMaximalValidValues(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('GET', '/user/trick/new');
        $kernelBrowser->submitForm('Save', [
            'trick[name]' => 'New trick',
            'trick[category]' => 1,
            'trick[description]' => 'New trick description',
            'trick[images]' => [1, 2, 3],
            'trick[videos]' => [1, 2],
        ]);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/admin/');

        $container = self::getContainer();
        /** @var Trick $trick */
        $trick = $container->get('doctrine')->getManager()->getRepository(Trick::class)->findOneBy(['name' => 'New trick']);
        $kernelBrowser->request('GET', '/trick/'.$trick->getId());
        $this->assertResponseStatusCodeSame(200);
    }

    public function testTrickEditPageWithoutLogin(): void
    {
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('GET', '/user/trick/1/edit');

        $this->assertResponseRedirects('/login');
    }

    public function testTrickEditPageWithUserLoginWithUnexistingTrick(): void
    {
        $kernelBrowser = $this->loginUser();
        $kernelBrowser->request('GET', '/user/trick/100/edit');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testTrickEditPageWithAdminLoginWithUnexistingTrick(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('GET', '/user/trick/100/edit');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testTrickEditPageWithUserLoginWithExistingTrick(): void
    {
        $kernelBrowser = $this->loginUser();
        $kernelBrowser->request('GET', '/user/trick/1/edit');
        $kernelBrowser->submitForm('Update', [
            'trick[name]' => 'https://www.youtube.com/watch?v=PEP1-Y7fX_I',
        ]);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/');
    }

    public function testTrickEditPageWithAdminLoginWithExistingTrick(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('GET', '/user/trick/1/edit');
        $kernelBrowser->submitForm('Update', [
            'trick[name]' => 'https://www.youtube.com/watch?v=PEP1-Y7fX_I',
        ]);

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/admin/');
    }

    public function testTrickRemovePageWithoutLogin(): void
    {
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('POST', '/user/trick/1');

        $this->assertResponseRedirects('/login');
    }

    public function testTrickRemovePageWithUserLoginWithUnexistingTrick(): void
    {
        $kernelBrowser = $this->loginUser();
        $kernelBrowser->request('POST', '/user/trick/100');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testTrickRemovePageWithAdminLoginWithUnexistingTrick(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('POST', '/user/trick/100');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testTrickRemovePageWithUserLoginWithExistingTrickWithoutAssociation(): void
    {
        $kernelBrowser = $this->loginUser();
        $kernelBrowser->request('POST', '/user/trick/2');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/');
    }

    public function testTrickRemovePageWithAdminLoginWithExistingTrickWithoutAssociation(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('POST', '/user/trick/2');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/admin/');
    }

    public function testTrickRemovePageWithUserLoginWithExistingTrickWithAssociation(): void
    {
        $kernelBrowser = $this->loginUser();
        $kernelBrowser->request('POST', '/user/trick/1');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/user/');
    }

    public function testTrickRemovePageWithAdminLoginWithExistingTrickWithAssociation(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('POST', '/user/trick/1');

        $this->assertResponseStatusCodeSame(303);
        $this->assertResponseRedirects('/admin/');
    }
}
