<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Trick;
use App\Repository\TrickRepository;
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

        return $this->render('_inc/_load_more_tricks.html.twig', [
            'tricks' => $results,
        ]);
    }

    #[Route('/tricks', name: 'tricks')]
    public function tricks(): Response
    {
        return new Response('Tricks');
    }

    #[Route('/trick/{id}', name: 'trick')]
    public function trick(Trick $trick): Response
    {
        return new Response('Trick');
    }
}
