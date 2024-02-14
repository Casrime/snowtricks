<?php

namespace App\Controller\User;

use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user/trick')]
class TrickController extends AbstractController
{
    #[Route('/new', name: 'app_trick_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($trick);
            $entityManager->flush();

            $this->addFlash('success', 'Trick created successfully');

            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('admin', [], Response::HTTP_SEE_OTHER);
            }

            return $this->redirectToRoute('user', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('common/new.html.twig', [
            'trick' => $trick,
            'form' => $form,
            'name' => 'trick',
        ]);
    }

    #[Route('/{id}/edit', name: 'app_trick_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Trick $trick, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setUpdatedAt(new DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('warning', 'Trick updated successfully');

            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('admin', [], Response::HTTP_SEE_OTHER);
            }

            return $this->redirectToRoute('user', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('common/edit.html.twig', [
            'trick' => $trick,
            'form' => $form,
            'name' => 'trick',
        ]);
    }

    #[Route('/{id}', name: 'app_trick_delete', methods: ['POST'])]
    public function delete(Request $request, Trick $trick, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trick->getId(), $request->request->get('_token'))) {
            foreach ($trick->getComments() as $comment) {
                $entityManager->remove($comment);
            }
            $entityManager->remove($trick);
            $entityManager->flush();
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('user', [], Response::HTTP_SEE_OTHER);
    }
}
