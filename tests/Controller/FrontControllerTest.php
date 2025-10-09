<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Token;
use Symfony\Component\Uid\Uuid;

final class FrontControllerTest extends BaseController
{
    public function testHomePage(): void
    {
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('GET', '/');

        $this->assertSelectorTextContains('h1', 'SnowTricks');
    }

    public function testTricksPage(): void
    {
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('GET', '/tricks');

        $this->assertSelectorTextNotContains('h1', 'SnowTricks');
        $this->assertSelectorTextContains('h1', 'Tricks list');
    }

    public function testTrickShowPageWithoutLogin(): void
    {
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('GET', '/trick/1');

        $this->assertResponseStatusCodeSame(200);
    }

    public function testTrickShowPageWithUserLogin(): void
    {
        $kernelBrowser = $this->loginUser();
        $kernelBrowser->request('GET', '/trick/1');

        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('h1', 'Mute');
    }

    public function testTrickShowPageWithAdminLogin(): void
    {
        $kernelBrowser = $this->loginAdmin();
        $kernelBrowser->request('GET', '/trick/1');

        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('h1', 'Mute');
    }

    public function testRegisterPageWithoutFormSubmission(): void
    {
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertPageTitleSame('Register');
        $this->assertSelectorTextSame('h1', 'Register');
    }

    public function testRegisterPageWithFormSubmissionWithEmptyValues(): void
    {
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('GET', '/register');

        $kernelBrowser->submitForm('Create an account');

        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorTextContains('#registration_form', 'Please enter a username');
        $this->assertSelectorTextContains('#registration_form', 'Please enter an email');
        $this->assertSelectorTextContains('#registration_form', 'Please enter a password');
    }

    public function testRegisterPageWithFormSubmissionWithInvalidValues(): void
    {
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('GET', '/register');

        $kernelBrowser->submitForm('Create an account', [
            'registration_form[username]' => '1',
            'registration_form[email]' => 'hello',
            'registration_form[plainPassword]' => '1234',
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorTextContains('#registration_form', 'Your username should be at least 3 characters');
        $this->assertSelectorTextContains('#registration_form', 'Please enter a valid email address');
        $this->assertSelectorTextContains('#registration_form', 'Your password should be at least 6 characters');
    }

    public function testRegisterPageWithFormSubmissionWithExistingValues(): void
    {
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('GET', '/register');

        $kernelBrowser->submitForm('Create an account', [
            'registration_form[username]' => 'admin',
            'registration_form[email]' => 'new-user@snowtricks.com',
            'registration_form[plainPassword]' => 'pass123',
        ]);

        $this->assertEquals('/register', $kernelBrowser->getRequest()->getPathInfo());

        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorTextContains('#registration_form p.text-red-600', 'There is already an account with this username.');
    }

    public function testRegisterPageWithFormSubmissionWithValidValues(): void
    {
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('GET', '/register');

        $kernelBrowser->submitForm('Create an account', [
            'registration_form[username]' => 'admin-for-test',
            'registration_form[email]' => 'admin-for-test@snowtricks.com',
            'registration_form[plainPassword]' => 'pass123',
        ]);

        $this->assertResponseStatusCodeSame(302);
        $kernelBrowser->followRedirect();

        $this->assertEquals('/', $kernelBrowser->getRequest()->getPathInfo());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'SnowTricks');
    }

    public function testActivatePageWithInvalidValue(): void
    {
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('GET', '/activate/123456');

        $this->assertResponseStatusCodeSame(500);
        $this->assertSelectorTextContains('h1.exception-message', 'Invalid UUID: "123456".');
    }

    public function testActivatePageWithInvalidToken(): void
    {
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('GET', '/activate/018cbbef-6aaf-7cc2-8229-2fc89c7d2b29');

        $this->assertResponseStatusCodeSame(302);
        $kernelBrowser->followRedirect();
        $this->assertEquals('/login', $kernelBrowser->getRequest()->getPathInfo());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('div.alert-danger', 'Invalid token!');
    }

    public function testActivatePageWithExpiredToken(): void
    {
        $kernelBrowser = self::createClient();
        /** @var Token[] $tokens */
        $tokens = $this->getDoctrine($kernelBrowser)->getRepository(Token::class)->findBy(['user' => 4], [
            'expirationDate' => 'ASC',
        ]);
        /** @var Uuid $tokenUuid */
        $tokenUuid = $tokens[0]->getUuid();
        $kernelBrowser->request('GET', '/activate/'.$tokenUuid->toRfc4122());

        $this->assertResponseStatusCodeSame(302);
        $kernelBrowser->followRedirect();
        $this->assertEquals('/login', $kernelBrowser->getRequest()->getPathInfo());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('div.alert-danger', 'Token expired!');
    }

    public function testActivatePageWithValidToken(): void
    {
        $kernelBrowser = self::createClient();
        /** @var Token[] $tokens */
        $tokens = $this->getDoctrine($kernelBrowser)->getRepository(Token::class)->findBy(['user' => 4], [
            'expirationDate' => 'ASC',
        ]);
        /** @var Uuid $tokenUuid */
        $tokenUuid = $tokens[1]->getUuid();
        $kernelBrowser->request('GET', '/activate/'.$tokenUuid->toRfc4122());

        $this->assertResponseStatusCodeSame(302);
        $kernelBrowser->followRedirect();
        $this->assertEquals('/login', $kernelBrowser->getRequest()->getPathInfo());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('div.alert-success', 'Your account has been activated!');
    }

    public function testLoginPageWithUnexistingUser(): void
    {
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('GET', '/login');

        $kernelBrowser->submitForm('Connexion', [
            'login[username]' => 'unexisting-user',
            'login[password]' => 'pass',
        ]);

        $this->assertResponseStatusCodeSame(302);
        $kernelBrowser->followRedirect();

        $this->assertEquals('/login', $kernelBrowser->getRequest()->getPathInfo());
        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('#invalid-credentials', 'Invalid credentials.');
    }

    public function testLoginPageWithValidUsernameAndInvalidPassword(): void
    {
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('GET', '/login');

        $kernelBrowser->submitForm('Connexion', [
            'login[username]' => 'admin',
            'login[password]' => 'pass',
        ]);

        $this->assertResponseStatusCodeSame(302);
        $kernelBrowser->followRedirect();

        $this->assertEquals('/login', $kernelBrowser->getRequest()->getPathInfo());
        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('#invalid-credentials', 'Invalid credentials.');
    }

    public function testLoginPageWithValidUsernameAndPassword(): void
    {
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('GET', '/login');

        $kernelBrowser->submitForm('Connexion', [
            'login[username]' => 'admin',
            'login[password]' => 'pass123',
        ]);

        $this->assertResponseStatusCodeSame(302);
        $kernelBrowser->followRedirect();

        $this->assertEquals('/', $kernelBrowser->getRequest()->getPathInfo());
        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('h1', 'SnowTricks');
    }

    public function testLogoutPage(): void
    {
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('GET', '/login');

        $kernelBrowser->submitForm('Connexion', [
            'login[username]' => 'admin',
            'login[password]' => 'pass123',
        ]);

        $this->assertResponseStatusCodeSame(302);
        $kernelBrowser->followRedirect();

        $kernelBrowser->request('GET', '/logout');
        $kernelBrowser->followRedirect();

        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('h1', 'SnowTricks');
    }

    public function testForgetPasswordPageWithoutFormSubmission(): void
    {
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('GET', '/forget_password');

        $this->assertSelectorTextContains('label', 'Username');
    }

    public function testForgetPasswordPageWithFormSubmissionWithInvalidUsername(): void
    {
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('GET', '/forget_password');

        $kernelBrowser->submitForm('Valider', [
            'forget_password[username]' => 'unexisting-user',
        ]);

        $this->assertResponseStatusCodeSame(302);
        $kernelBrowser->followRedirect();
        $this->assertEquals('/login', $kernelBrowser->getRequest()->getPathInfo());

        $this->assertSelectorTextContains('div.alert-danger', 'Aucun compte n\'est associé à ce nom d\'utilisateur.');
    }

    public function testForgetPasswordPageWithFormSubmissionWithValidUsername(): void
    {
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('GET', '/forget_password');

        $kernelBrowser->submitForm('Valider', [
            'forget_password[username]' => 'admin',
        ]);

        $this->assertResponseStatusCodeSame(302);
        $kernelBrowser->followRedirect();
        $this->assertEquals('/login', $kernelBrowser->getRequest()->getPathInfo());

        $this->assertSelectorTextContains('div.alert-success', 'Un email vous a été envoyé pour réinitialiser votre mot de passe.');
    }

    public function testResetPasswordPageWithInvalidToken(): void
    {
        $kernelBrowser = self::createClient();
        $kernelBrowser->request('GET', '/reset_password/018cbbef-6aaf-7cc2-8229-2fc89c7d2b29');

        $this->assertResponseStatusCodeSame(302);
        $kernelBrowser->followRedirect();
        $this->assertEquals('/login', $kernelBrowser->getRequest()->getPathInfo());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('div.alert-danger', 'Invalid token!');
    }

    public function testResetPasswordPageWithExpiredToken(): void
    {
        $kernelBrowser = self::createClient();
        /** @var Token[] $tokens */
        $tokens = $this->getDoctrine($kernelBrowser)->getRepository(Token::class)->findBy(['user' => 4], [
            'expirationDate' => 'ASC',
        ]);
        /** @var Uuid $tokenUuid */
        $tokenUuid = $tokens[0]->getUuid();
        $kernelBrowser->request('GET', '/reset_password/'.$tokenUuid->toRfc4122());

        $this->assertResponseStatusCodeSame(302);
        $kernelBrowser->followRedirect();
        $this->assertEquals('/login', $kernelBrowser->getRequest()->getPathInfo());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('div.alert-danger', 'Token expired!');
    }

    public function testResetPasswordPageWithValidToken(): void
    {
        $kernelBrowser = self::createClient();
        /** @var Token[] $tokens */
        $tokens = $this->getDoctrine($kernelBrowser)->getRepository(Token::class)->findBy(['user' => 4], [
            'expirationDate' => 'ASC',
        ]);
        /** @var Uuid $tokenUuid */
        $tokenUuid = $tokens[1]->getUuid();
        $kernelBrowser->request('GET', '/reset_password/'.$tokenUuid->toRfc4122());

        $kernelBrowser->submitForm('Reset', [
            'reset_password[plainPassword][first]' => 'p@ss123',
            'reset_password[plainPassword][second]' => 'p@ss123',
        ]);

        $this->assertResponseStatusCodeSame(302);
        $kernelBrowser->followRedirect();
        $this->assertEquals('/', $kernelBrowser->getRequest()->getPathInfo());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('div.alert-success', 'Votre mot de passe a été réinitialisé.');
    }
}
