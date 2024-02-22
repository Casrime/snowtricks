<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Token;
use Symfony\Component\Uid\Uuid;

class FrontControllerTest extends BaseController
{
    public function testHomePage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertSelectorTextContains('h1', 'SnowTricks');
    }

    public function testTricksPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tricks');

        $this->assertSelectorTextNotContains('h1', 'SnowTricks');
        $this->assertSelectorTextContains('h1', 'Tricks list');
    }

    public function testTrickShowPageWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/trick/mute');

        $this->assertResponseStatusCodeSame(200);
    }

    public function testTrickShowPageWithUserLogin(): void
    {
        $client = $this->loginUser();
        $client->request('GET', '/trick/mute');

        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('h1', 'Mute');
    }

    public function testTrickShowPageWithAdminLogin(): void
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/trick/mute');

        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('h1', 'Mute');
    }

    public function testRegisterPageWithoutFormSubmission(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertPageTitleSame('Register');
        $this->assertSelectorTextSame('h1', 'Register');
    }

    public function testRegisterPageWithFormSubmissionWithEmptyValues(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');

        $client->submitForm('Create an account');

        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorTextContains('#registration_form', 'Please enter a username');
        $this->assertSelectorTextContains('#registration_form', 'Please enter an email');
        $this->assertSelectorTextContains('#registration_form', 'Please enter a password');
    }

    public function testRegisterPageWithFormSubmissionWithInvalidValues(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');

        $client->submitForm('Create an account', [
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
        $client = static::createClient();
        $client->request('GET', '/register');

        $client->submitForm('Create an account', [
            'registration_form[username]' => 'admin',
            'registration_form[email]' => 'new-user@snowtricks.com',
            'registration_form[plainPassword]' => 'pass123',
        ]);

        $this->assertEquals('/register', $client->getRequest()->getPathInfo());

        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorTextContains('.invalid-feedback', 'There is already an account with this username.');
    }

    public function testRegisterPageWithFormSubmissionWithValidValues(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');

        $client->submitForm('Create an account', [
            'registration_form[username]' => 'admin-for-test',
            'registration_form[email]' => 'admin-for-test@snowtricks.com',
            'registration_form[plainPassword]' => 'pass123',
        ]);

        $this->assertResponseStatusCodeSame(302);
        $client->followRedirect();

        $this->assertEquals('/', $client->getRequest()->getPathInfo());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'SnowTricks');
    }

    public function testActivatePageWithInvalidValue(): void
    {
        $client = static::createClient();
        $client->request('GET', '/activate/123456');

        $this->assertResponseStatusCodeSame(500);
        $this->assertSelectorTextContains('h1.exception-message', 'Invalid UUID: "123456".');
    }

    public function testActivatePageWithInvalidToken(): void
    {
        $client = static::createClient();
        $client->request('GET', '/activate/018cbbef-6aaf-7cc2-8229-2fc89c7d2b29');

        $this->assertResponseStatusCodeSame(302);
        $client->followRedirect();
        $this->assertEquals('/login', $client->getRequest()->getPathInfo());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('div.alert-danger', 'Invalid token!');
    }

    public function testActivatePageWithExpiredToken(): void
    {
        $client = static::createClient();
        /** @var Token[] $tokens */
        $tokens = $this->getDoctrine($client)->getRepository(Token::class)->findBy(['user' => 4], [
            'expirationDate' => 'ASC',
        ]);
        /** @var Uuid $tokenUuid */
        $tokenUuid = $tokens[0]->getUuid();
        $client->request('GET', '/activate/'.$tokenUuid->toRfc4122());

        $this->assertResponseStatusCodeSame(302);
        $client->followRedirect();
        $this->assertEquals('/login', $client->getRequest()->getPathInfo());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('div.alert-danger', 'Token expired!');
    }

    public function testActivatePageWithValidToken(): void
    {
        $client = static::createClient();
        /** @var Token[] $tokens */
        $tokens = $this->getDoctrine($client)->getRepository(Token::class)->findBy(['user' => 4], [
            'expirationDate' => 'ASC',
        ]);
        /** @var Uuid $tokenUuid */
        $tokenUuid = $tokens[1]->getUuid();
        $client->request('GET', '/activate/'.$tokenUuid->toRfc4122());

        $this->assertResponseStatusCodeSame(302);
        $client->followRedirect();
        $this->assertEquals('/login', $client->getRequest()->getPathInfo());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('div.alert-success', 'Your account has been activated!');
    }

    public function testLoginPageWithUnexistingUser(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $client->submitForm('Connexion', [
            'login[username]' => 'unexisting-user',
            'login[password]' => 'pass',
        ]);

        $this->assertResponseStatusCodeSame(302);
        $client->followRedirect();

        $this->assertEquals('/login', $client->getRequest()->getPathInfo());
        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('div.alert-danger', 'Invalid credentials.');
    }

    public function testLoginPageWithValidUsernameAndInvalidPassword(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $client->submitForm('Connexion', [
            'login[username]' => 'admin',
            'login[password]' => 'pass',
        ]);

        $this->assertResponseStatusCodeSame(302);
        $client->followRedirect();

        $this->assertEquals('/login', $client->getRequest()->getPathInfo());
        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('div.alert-danger', 'Invalid credentials.');
    }

    public function testLoginPageWithValidUsernameAndPassword(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $client->submitForm('Connexion', [
            'login[username]' => 'admin',
            'login[password]' => 'pass123',
        ]);

        $this->assertResponseStatusCodeSame(302);
        $client->followRedirect();

        $this->assertEquals('/', $client->getRequest()->getPathInfo());
        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('h1', 'SnowTricks');
    }

    public function testLogoutPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $client->submitForm('Connexion', [
            'login[username]' => 'admin',
            'login[password]' => 'pass123',
        ]);

        $this->assertResponseStatusCodeSame(302);
        $client->followRedirect();

        $client->request('GET', '/logout');
        $client->followRedirect();

        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('h1', 'SnowTricks');
    }

    public function testForgetPasswordPageWithoutFormSubmission(): void
    {
        $client = static::createClient();
        $client->request('GET', '/forget_password');

        $this->assertSelectorTextContains('label', 'Username');
    }

    public function testForgetPasswordPageWithFormSubmissionWithInvalidUsername(): void
    {
        $client = static::createClient();
        $client->request('GET', '/forget_password');

        $client->submitForm('Valider', [
            'forget_password[username]' => 'unexisting-user',
        ]);

        $this->assertResponseStatusCodeSame(302);
        $client->followRedirect();
        $this->assertEquals('/login', $client->getRequest()->getPathInfo());

        $this->assertSelectorTextContains('div.alert-danger', 'Aucun compte n\'est associé à ce nom d\'utilisateur.');
    }

    public function testForgetPasswordPageWithFormSubmissionWithValidUsername(): void
    {
        $client = static::createClient();
        $client->request('GET', '/forget_password');

        $client->submitForm('Valider', [
            'forget_password[username]' => 'admin',
        ]);

        $this->assertResponseStatusCodeSame(302);
        $client->followRedirect();
        $this->assertEquals('/login', $client->getRequest()->getPathInfo());

        $this->assertSelectorTextContains('div.alert-success', 'Un email vous a été envoyé pour réinitialiser votre mot de passe.');
    }

    public function testResetPasswordPageWithInvalidToken(): void
    {
        $client = static::createClient();
        $client->request('GET', '/reset_password/018cbbef-6aaf-7cc2-8229-2fc89c7d2b29');

        $this->assertResponseStatusCodeSame(302);
        $client->followRedirect();
        $this->assertEquals('/login', $client->getRequest()->getPathInfo());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('div.alert-danger', 'Invalid token!');
    }

    public function testResetPasswordPageWithExpiredToken(): void
    {
        $client = static::createClient();
        /** @var Token[] $tokens */
        $tokens = $this->getDoctrine($client)->getRepository(Token::class)->findBy(['user' => 4], [
            'expirationDate' => 'ASC',
        ]);
        /** @var Uuid $tokenUuid */
        $tokenUuid = $tokens[0]->getUuid();
        $client->request('GET', '/reset_password/'.$tokenUuid->toRfc4122());

        $this->assertResponseStatusCodeSame(302);
        $client->followRedirect();
        $this->assertEquals('/login', $client->getRequest()->getPathInfo());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('div.alert-danger', 'Token expired!');
    }

    public function testResetPasswordPageWithValidToken(): void
    {
        $client = static::createClient();
        /** @var Token[] $tokens */
        $tokens = $this->getDoctrine($client)->getRepository(Token::class)->findBy(['user' => 4], [
            'expirationDate' => 'ASC',
        ]);
        /** @var Uuid $tokenUuid */
        $tokenUuid = $tokens[1]->getUuid();
        $client->request('GET', '/reset_password/'.$tokenUuid->toRfc4122());

        $client->submitForm('Reset', [
            'reset_password[plainPassword][first]' => 'p@ss123',
            'reset_password[plainPassword][second]' => 'p@ss123',
        ]);

        $this->assertResponseStatusCodeSame(302);
        $client->followRedirect();
        $this->assertEquals('/', $client->getRequest()->getPathInfo());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('div.alert-success', 'Votre mot de passe a été réinitialisé.');
    }
}
