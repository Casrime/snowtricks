<?php

declare(strict_types=1);

namespace App\Tests\Controller\User;

use App\Entity\Token;
use App\Tests\Controller\BaseController;
use Symfony\Component\Uid\Uuid;

class LoginControllerTest extends BaseController
{
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
