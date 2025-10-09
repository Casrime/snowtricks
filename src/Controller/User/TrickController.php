<?php

namespace App\Controller\User;

use App\Entity\Trick;
use App\Entity\User;
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
            /** @var User $user */
            $user = $this->getUser();
            $trick->setUser($user);
            $entityManager->persist($trick);
            $entityManager->flush();

            $this->addFlash('success', sprintf('Trick created successfully: %s', $trick->getName()));

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

            $this->addFlash('warning', sprintf('Trick updated successfully: %s', $trick->getName()));

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
        if ($this->isCsrfTokenValid('delete'.$trick->getId(), $request->request->getString('_token'))) {
            foreach ($trick->getComments() as $comment) {
                $entityManager->remove($comment);
            }
            $entityManager->remove($trick);
            $entityManager->flush();

            $this->addFlash('danger', sprintf('Trick removed successfully: %s', $trick->getName()));
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('user', [], Response::HTTP_SEE_OTHER);
    }
}
