<?php

namespace App\Controller\User;

use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use App\Service\FileHandler;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/user/trick')]
class TrickController extends AbstractController
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    #[Route('/new', name: 'app_trick_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FileHandler $fileHandler): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ?UploadedFile $mainImageFile */
            $mainImageFile = $trick->getMainImage()?->getFile();
            if ($mainImageFile) {
                $imageFileName = $fileHandler->upload($mainImageFile);
                $trick->getMainImage()->setName($imageFileName);
                $entityManager->persist($trick->getMainImage());
            }
            foreach ($trick->getImages() as $image) {
                /** @var ?UploadedFile $imageFile */
                $imageFile = $image->getFile();
                if ($imageFile) {
                    $imageFileName = $fileHandler->upload($imageFile);
                    $image->setName($imageFileName);
                    $entityManager->persist($image);
                }
            }
            $trick->setSlug($this->slugger->slug($trick->getName())->lower());
            $trick->setUser($this->getUser());
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

    #[Route('/{slug}/edit', name: 'app_trick_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Trick $trick, EntityManagerInterface $entityManager, FileHandler $fileHandler): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ?UploadedFile $mainImageFile */
            $mainImageFile = $trick->getMainImage()?->getFile();
            if ($mainImageFile) {
                $imageFileName = $fileHandler->upload($mainImageFile);
                $trick->getMainImage()->setName($imageFileName);
                $entityManager->persist($trick->getMainImage());
            }
            foreach ($trick->getImages() as $image) {
                /** @var ?UploadedFile $imageFile */
                $imageFile = $image->getFile();
                if ($imageFile) {
                    $imageFileName = $fileHandler->upload($imageFile);
                    $image->setName($imageFileName);
                    $entityManager->persist($image);
                }
            }
            $trick->setSlug($this->slugger->slug($trick->getName())->lower());
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

    #[Route('/{slug}', name: 'app_trick_delete', methods: ['POST'])]
    public function delete(Request $request, Trick $trick, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trick->getId(), $request->request->get('_token'))) {
            $entityManager->remove($trick);
            $entityManager->flush();
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('user', [], Response::HTTP_SEE_OTHER);
    }
}
