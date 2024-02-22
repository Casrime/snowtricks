<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Token;
use App\Entity\Trick;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\ForgetPasswordType;
use App\Form\LoginType;
use App\Form\RegistrationFormType;
use App\Form\ResetPasswordType;
use App\Repository\CommentRepository;
use App\Repository\TokenRepository;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Uid\Uuid;

final class FrontController extends BaseController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('front/index.html.twig');
    }

    #[Route('/load-more-tricks', name: 'load-more-tricks')]
    public function loadMoreTricks(Request $request, TrickRepository $trickRepository): Response
    {
        $tricks = $trickRepository->loadMoreTricks($request->query->getInt('offset'));
        $results = $tricks->getQuery()->getResult();

        return $this->render('_inc/_tricks.html.twig', [
            'tricks' => $results,
        ]);
    }

    #[Route('/tricks', name: 'tricks')]
    public function tricks(TrickRepository $trickRepository): Response
    {
        return $this->render('front/tricks.html.twig', [
            'tricks' => $trickRepository->findAll(),
        ]);
    }

    #[Route('/trick/{slug}', name: 'trick')]
    public function trick(Request $request, Trick $trick, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setTrick($trick);
            $comment->setUser($this->getUser());

            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire ajouté !');

            return $this->redirectToRoute('trick', ['id' => $trick->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('front/trick.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    #[Route('/trick/{id}/load-more-comments', name: 'load-more-comments')]
    public function loadMoreComments(Request $request, Trick $trick, CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->loadMoreComments($trick, $request->query->getInt('offset'));
        $results = $comments->getQuery()->getResult();

        return $this->render('_inc/_comments.html.twig', [
            'comments' => $results,
        ]);
    }

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

        return $this->render('front/register.html.twig', [
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

    #[Route('/login', name: 'app_login')]
    public function login(Request $request, AuthenticationUtils $authenticationUtils, Security $security): Response
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

        return $this->render('front/login.html.twig', [
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

        return $this->render('front/forget_password.html.twig', [
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

        return $this->render('front/reset_password.html.twig', [
            'form' => $form,
        ]);
    }
}
