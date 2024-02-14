<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Repository\CommentRepository;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use App\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'user', methods: ['GET'])]
    public function index(
        TrickRepository $trickRepository,
        CommentRepository $commentRepository,
        ImageRepository $imageRepository,
        VideoRepository $videoRepository
    ): Response
    {
        return $this->render('user/index.html.twig', [
            'tricks' => $trickRepository->findBy([
                'user' => $this->getUser(),
            ]),
            'comments' => $commentRepository->findBy([
                'user' => $this->getUser(),
            ]),
            'images' => $imageRepository->findAll(),
            'videos' => $videoRepository->findAll(),
        ]);
    }
}
