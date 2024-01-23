<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Token;
use App\Entity\User;
use App\Form\ForgetPasswordType;
use App\Form\LoginType;
use App\Form\ResetPasswordType;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Uid\Uuid;

class LoginController extends BaseController
{
    #[Route('/login', name: 'app_login')]
    public function index(Request $request, AuthenticationUtils $authenticationUtils, Security $security): Response
    {
        $form = $this->createForm(LoginType::class);
        $form->handleRequest($request);

        if (null !== $security->getUser()) {
            return $this->redirectToRoute('home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'form' => $form,
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): never
    {
        // controller can be blank: it will never be called!
        throw new Exception('Don\'t forget to activate logout in security.yaml');
    }

    #[Route('/forget_password', name: 'app_forget_password', methods: ['GET', 'POST'])]
    public function forgetPassword(Request $request, UserRepository $userRepository): Response
    {
        $form = $this->createForm(ForgetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $userExists = $userRepository->findOneBy(['username' => $user->getUsername()]);

            if ($userExists) {
                $token = new Token();
                $token->setUser($userExists);
                $token->setExpirationDate(new DateTimeImmutable('+7 days'));
                $token->setActive(true);
                $this->getEntityManager()->persist($token);
                $this->getEntityManager()->flush();

                /** @var Uuid $tokenUuid */
                $tokenUuid = $token->getUuid();

                /** @var string $email */
                $email = $userExists->getEmail() ?? '';

                $this->getMail()->send($email, 'Reset your password', 'email/forget_password.html.twig', [
                    'token' => $tokenUuid->toRfc4122(),
                    'expiration_date' => $token->getExpirationDate(),
                    'user' => $userExists,
                    'baseUrl' => $request->getSchemeAndHttpHost(),
                ]);
                $this->addFlash('success', 'Un email vous a été envoyé pour réinitialiser votre mot de passe.');
            } else {
                $this->addFlash('danger', 'Aucun compte n\'est associé à ce nom d\'utilisateur.');
            }

            return $this->redirectToRoute('app_login');
        }

        return $this->render('login/forget_password.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/reset_password/{value}', name: 'app_reset_password', methods: ['GET', 'POST'])]
    public function resetPassword(Request $request, TokenRepository $tokenRepository): Response
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

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();
            /** @var User $user */
            $user = $token->getUser();
            $user->setPassword($this->getUserPasswordHasher()->hashPassword($user, $plainPassword));
            $token->setActive(false);

            $entityManager = $this->getEntityManager();
            $entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a été réinitialisé.');

            return $this->redirectToRoute('home');
        }

        return $this->render('login/reset_password.html.twig', [
            'form' => $form,
        ]);
    }
}
