<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin', methods: ['GET'])]
    public function index(
        TrickRepository $trickRepository,
        CategoryRepository $categoryRepository,
        UserRepository $userRepository,
        CommentRepository $commentRepository,
        ImageRepository $imageRepository,
        VideoRepository $videoRepository
    ): Response
    {
        return $this->render('admin/index.html.twig', [
            'tricks' => $trickRepository->findAll(),
            'categories' => $categoryRepository->findAll(),
            'users' => $userRepository->findAll(),
            'comments' => $commentRepository->findAll(),
            'images' => $imageRepository->findAll(),
            'videos' => $videoRepository->findAll(),
        ]);
    }

    #[Route('/user/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if ($currentUser->getId() === $user->getId()) {
            $this->addFlash('danger', 'You cannot delete the logged in user.');
            return $this->redirectToRoute('admin', [], Response::HTTP_SEE_OTHER);
        }
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'User deleted with associated tokens, tricks and comments.');
        }

        return $this->redirectToRoute('admin', [], Response::HTTP_SEE_OTHER);
    }
}
