<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Token;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\TokenRepository;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Uid\Uuid;

class RegistrationController extends BaseController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request): Response
    {
        $form = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();
            /** @var User $user */
            $user = $form->getData();
            $user->setRoles(['ROLE_USER']);
            $user->setPassword(
                $this->getUserPasswordHasher()->hashPassword(
                    $user,
                    $plainPassword
                )
            );

            $this->getEntityManager()->persist($user);

            $token = new Token();
            $token->setUser($user);
            $token->setExpirationDate(new DateTimeImmutable('+7 days'));
            $token->setActive(true);
            $this->getEntityManager()->persist($token);
            $this->getEntityManager()->flush();

            /** @var Uuid $tokenUuid */
            $tokenUuid = $token->getUuid();

            /** @var string $email */
            $email = $user->getEmail() ?? '';

            $this->getMail()->send($email, 'Activate your account', 'email/register.html.twig', [
                'token' => $tokenUuid->toRfc4122(),
                'expiration_date' => $token->getExpirationDate(),
                'user' => $user,
                'baseUrl' => $request->getSchemeAndHttpHost(),
            ]);

            $this->addFlash('success', 'Account created, check your email to activate your account!');

            return $this->redirectToRoute('home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/activate/{value}', name: 'app_activate')]
    public function activate(Request $request, TokenRepository $tokenRepository): Response
    {
        /** @var string $value */
        $value = $request->get('value', '');

        $token = $tokenRepository->findOneBy([
            'uuid' => Uuid::fromString($value),
            'active' => true,
        ]);

        if (null === $token) {
            $this->addFlash('danger', 'Invalid token!');
            throw new TokenNotFoundException();
        }

        if ($token->getExpirationDate() < new DateTimeImmutable()) {
            $this->addFlash('danger', 'Token expired!');
            throw new TokenNotFoundException();
        }

        /** @var User $user */
        $user = $token->getUser();

        $user->setActive(true);
        $token->setActive(false);
        $this->getEntityManager()->flush();

        $this->addFlash('success', 'Your account has been activated!');

        return $this->redirectToRoute('app_login');
    }
}
