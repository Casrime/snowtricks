<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Trick;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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

    #[Route('/trick/{id}', name: 'trick')]
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
}
